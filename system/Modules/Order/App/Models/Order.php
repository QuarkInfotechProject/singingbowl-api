<?php

namespace Modules\Order\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Coupon\App\Models\Coupon;
use Modules\OrderProcessing\App\Models\OrderPathaoConsignment;
use Modules\OrderProcessing\App\Models\Refund;
use Modules\Payment\HasTransactionReference;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Transaction\App\Models\Transaction;
use Modules\User\App\Models\User;

class Order extends Model
{
    use HasFactory, SoftDeletes;


    const PENDING_PAYMENT = 'pending_payment';
        const ORDER_PLACED = 'processing';
    const ON_HOLD = 'on_hold';

    const  DELIVERED = 'completed';
    const  CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';

    const FAILED = 'failed';

    // const NCELL_ORDER = 'ncell_order';
    const FAILED_DELIVERY = 'failed_delivery';
    const AWAITING_REFUND = 'awaiting_refund';
    const PARTIALLY_REFUNDED = 'partially_refund';
    const SHIPPED = 'shipped';
    const READY_TO_SHIP = 'shipment';
    const DRAFT = 'draft';

    public static $orderStatusMapping = [
        self::PENDING_PAYMENT => 'Pending Payment',
        self::ORDER_PLACED => 'Order Placed',
        self::ON_HOLD => 'On Hold',
        self::DELIVERED => 'Delivered',
        self::CANCELLED => 'Cancelled',
        self::REFUNDED => 'Refunded',
        self::FAILED => 'Failed',
        // self::NCELL_ORDER => 'Ncell Order',
        self::FAILED_DELIVERY => 'Failed Delivery',
        self::AWAITING_REFUND => 'Awaiting Refund',
        self::PARTIALLY_REFUNDED => 'Partially Refunded',
        self::SHIPPED => 'Shipped',
        self::READY_TO_SHIP => 'Ready To Ship',
        self::DRAFT => 'Draft',
    ];

    public static $orderFilterMapping = [
        self::PENDING_PAYMENT => 'Pending Payment',
        self::DELIVERED => 'Delivered',
        self::CANCELLED => 'Cancelled',
        self::REFUNDED => 'Refunded',
        self::FAILED => 'Failed',
        self::FAILED_DELIVERY => 'Failed Delivery',
        self::SHIPPED => 'Shipped',
        self::READY_TO_SHIP => 'Ready To Ship',
        self::ORDER_PLACED => 'Order Placed',
    ];
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'total',
        'note',
        'payment_method',
        'status',
        'cancelled_date'
    ];

    protected $dates = ['cancelled_date', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderAddress()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'order_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function orderLog()
    {
        return $this->hasMany(OrderLog::class);
    }

    public function storeTransaction($response)
    {
        if (!$response instanceof HasTransactionReference) {
            return null;
        }

        $transactionData = $response->getTransactionReference();

        try {
            DB::beginTransaction();

            $transaction = $this->transaction()->create([
                'transaction_code' => $transactionData['transaction_code'],
                'status' => $transactionData['status'],
                'payment_method' => $this->attributes['payment_method'] === 'card'
                    ? 'card/' . $transactionData['cardName']
                    : $this->attributes['payment_method']
            ]);

            // if ($this->attributes['payment_method'] === 'khalti') {
            //     $this->validateTransactionStatus($transactionData);
            // }

            // if ($this->attributes['payment_method'] === 'card') {
            //     $this->validateCardTransactionStatus($transactionData);
            // }

            DB::commit();
            return $transaction;
        } catch (QueryException $exception) {
            DB::rollBack();
            $this->handleQueryException($exception);
        }
    }

    private function validateCardTransactionStatus(array $transactionData): void
    {
        if (isset($transactionData['reason']) && $transactionData['status'] !== 'COMPLETE') {
            throw new Exception(
                "{$transactionData['reason']}",
                ErrorCode::BAD_REQUEST
            );
        }
    }

    private function validateTransactionStatus(array $transactionData): void
    {
        if ($transactionData['statusCode'] === 400) {
            throw new Exception(
                "Payment failed. Status: {$transactionData['status']}.",
                ErrorCode::BAD_REQUEST
            );
        }

        if ($transactionData['statusCode'] === 200 && $transactionData['status'] !== 'Completed') {
            throw new Exception(
                "Payment is in {$transactionData['status']} state.",
                ErrorCode::BAD_REQUEST
            );
        }
    }

    private function handleQueryException(QueryException $exception): void
    {
        if ($exception->getCode() === '23000') {
            throw new Exception(
                'Duplicate transaction committed.',
                ErrorCode::BAD_REQUEST
            );
        }

        throw $exception;
    }

    public function getIsPaidAttribute() {

        if ($this->payment_method === 'cod' && $this->status === Order::DELIVERED) {
            return 'Yes';
        }

        return $this->transaction ? 'Yes' : 'No';
    }

    public function consignmentId()
    {
        return $this->hasOne(OrderPathaoConsignment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->sum('amount');
    }

    public function getRemainingRefundableAmountAttribute()
    {
        return $this->total - $this->total_refunded;
    }

    public function getIsRefundedAttribute()
    {
        return $this->refunds()->exists();
    }
}

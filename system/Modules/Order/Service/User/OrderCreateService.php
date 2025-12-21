<?php

namespace Modules\Order\Service\User;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Address\App\Models\Address;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\Service\CartClearService;
use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\CheckCartQuantity;
use Modules\Coupon\Checkers\CouponExists;
use Modules\Coupon\Checkers\ExcludedCoupons;
use Modules\Coupon\Checkers\ExcludedProducts;
use Modules\Coupon\Checkers\MinimumSpend;
use Modules\Coupon\Checkers\PaymentMethodCheck;
use Modules\Coupon\Checkers\RelatedCoupons;
use Modules\Coupon\Checkers\StackableCoupon;
use Modules\Coupon\Checkers\UsageLimitPerCoupon;
use Modules\Coupon\Checkers\UsageLimitPerCustomer;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Events\SendOrderInvoiceMail;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\App\Models\OrderCoupon;
use Modules\Order\App\Models\OrderItem;
use Modules\Order\DTO\SendOrderInvoiceEmailDTO;
use Modules\Payment\Facades\Gateway;
use Modules\Product\App\Notifications\ProductLowStockNotification;
use Modules\Product\App\Notifications\ProductOutOfStockNotification;
use Modules\Shared\Constant\GatewayConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class OrderCreateService
{

    private $checkers = [
        CouponExists::class,
        MinimumSpend::class,
        ValidCoupon::class,
        UsageLimitPerCoupon::class,
        UsageLimitPerCustomer::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
        CheckCartQuantity::class,
        StackableCoupon::class,
        RelatedCoupons::class,
        ExcludedCoupons::class,
        PaymentMethodCheck::class
    ];

    function __construct
    (
        private CartClearService $cartClearService,
        private OrderShowService $orderShowService
    )
    {
    }

    function create($request)
    {
        $userId = Auth::id();
        $cart = Cart::getForCurrentUser();

        if (!$cart || $cart->items->isEmpty()) {
            throw new Exception('Cart is empty.', ErrorCode::NOT_FOUND);
        }

        $paymentMethod = $request->input('paymentMethod');

        $address = $this->validateAddress($request->input('addressId'));
        $this->checkStock($cart->items);

        // Get coupon codes from request, or if not provided, get them from the cart
        $couponCodes = $request->input('couponCodes', []);

        // If no coupon codes provided in request, get them from cart
        if (empty($couponCodes) && $cart->hasAppliedCoupons()) {
            $couponCodes = $cart->getAppliedCoupons()->pluck('code')->toArray();
        }

        return DB::transaction(function () use ($userId, $cart, $address, $couponCodes, $paymentMethod, $request) {
            $orderData = $this->applyDiscounts($cart, $couponCodes, $paymentMethod);
            $order = $this->createOrder($userId, $cart, $orderData, $request->input('note'), $paymentMethod);
            $this->createOrderItems($order, $cart->items);
            $this->logOrderCreation($order);
            $this->saveOrderAddress($userId, $order, $address);
            $this->reduceStock($cart->items);

            $this->sendOrderInvoice($userId, $order);

            if ($paymentMethod === GatewayConstant::COD) {
                $gateway = Gateway::get(GatewayConstant::COD);
                $purchaseResult = $gateway->purchase($order, $request)->toArray();
                $this->cartClearService->clearCart('user', $userId);
                return $purchaseResult;
            } elseif ($paymentMethod === GatewayConstant::GETPAY) {
                $gateway = Gateway::get(GatewayConstant::GETPAY);
                $purchaseResult = $gateway->purchase($order, $request);
                // Don't clear cart yet - will be cleared after successful payment callback
                return $purchaseResult;
            } else {
                Log::warning(
                    'Attempt to use unsupported payment method.',
                    ['paymentMethod' => $paymentMethod, 'orderId' => $order->id]
                );
                throw new Exception(
                    "The payment method '{$paymentMethod}' is not currently supported.",
                    ErrorCode::BAD_REQUEST
                );
            }
        });
    }

    private function applyDiscounts($cart, $couponCodes, $paymentMethod)
    {
        $subtotal = $cart->subTotal();
        $discount = 0;
        $appliedCoupons = [];

        foreach ($couponCodes as $couponCode) {
            $coupon = Coupon::findByCode($couponCode);
            if (!$coupon) {
                throw new Exception("Coupon: {$couponCode} not found.", ErrorCode::NOT_FOUND);
            }

            $this->validateCoupon($coupon, $cart, $paymentMethod);

            $discountAmount = $cart->calculateDiscountAmount($coupon);

            $discount += $discountAmount;
            $appliedCoupons[] = $coupon;
        }

        $totalAfterDiscount = $cart->subTotal() - $discount;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $totalAfterDiscount,
            'appliedCoupons' => $appliedCoupons,
        ];
    }

    private function validateCoupon($coupon, $cart, $paymentMethod)
    {
        $result = resolve(Pipeline::class)
            ->send(['coupon' => $coupon, 'cart' => $cart, 'paymentMethod' => $paymentMethod])
            ->through($this->checkers)
            ->thenReturn();
    }

    private function createOrder($userId, $cart, $orderData, $note, $paymentMethod)
    {
        $order = Order::create([
            'user_id' => $userId,
            'subtotal' => $orderData['subtotal'],
            'discount' => $orderData['discount'],
            'total' => $orderData['total'],
            'note' => $note,
            'payment_method' => $paymentMethod,
            'status' => $paymentMethod === GatewayConstant::COD ? Order::ORDER_PLACED : Order::PENDING_PAYMENT,
        ]);

        try {
            foreach ($orderData['appliedCoupons'] as $coupon) {
                // Get the actual discount amount that was calculated and stored in the cart
                $cartCoupon = $cart->coupons()->where('coupons.id', $coupon->id)->first();
                $discountAmount = $cartCoupon ? $cartCoupon->pivot->discount_amount : 0;

                OrderCoupon::create([
                    'order_id' => $order->id,
                    'coupon_id' => $coupon->id,
                    'discount_amount' => $discountAmount,
                ]);
                $coupon->increment('used');
            }
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23000) {
                throw new Exception("Coupon {$coupon->code} has already been applied.", ErrorCode::UNPROCESSABLE_CONTENT);
            }
            throw $exception;
        }

        return $order;
    }

    private function createOrderItems($order, $cartItems)
    {
        $createdOrderItems = [];

        foreach ($cartItems as $cartItem) {
            $priceSource = $cartItem->variant ?? $cartItem->product;
            $unitPrice = $priceSource->original_price;

            if ($priceSource->special_price &&
                (is_null($priceSource->special_price_start) || now()->gte($priceSource->special_price_start)) &&
                (is_null($priceSource->special_price_end) || now()->lte($priceSource->special_price_end))) {
                $unitPrice = $priceSource->special_price;
            }

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'variant_id' => $cartItem->variant_id ?? null,
                'quantity' => $cartItem->quantity,
                'unit_price' => $unitPrice,
                'line_total' => $cartItem->quantity * $unitPrice,
            ]);

            $order->orderItems()->save($orderItem);

            $createdOrderItems[] = $orderItem;
        }

        return $createdOrderItems;
    }

    private function saveOrderAddress($userId, Order $order, $address)
    {
        OrderAddress::create([
            'user_id' => $userId,
            'order_id' => $order->id,
            'address_id' => $address->id,
        ]);
    }

    private function checkStock($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $item = $cartItem->product->has_variant ? $cartItem->variant : $cartItem->product;

            if (!$item->in_stock || $item->quantity <= 0) {
                $itemName = $cartItem->product->has_variant ? $cartItem->variant->name : $cartItem->product->product_name;
                $this->notifyOutofStock($item);
                throw new Exception("Sorry, {$itemName} is currently out of stock.", ErrorCode::FORBIDDEN);
            }

            if ($item->quantity <= 10) {
                $this->notifyLowStock($item);
            }
        }
    }

    private function validateAddress(string $id)
    {
        $address = Address::where('uuid', $id)
                    ->first();

        if (!$address) {
            Log::error('Address not found.', ['addressId' => $id]);
            throw new Exception('Address not found.', ErrorCode::NOT_FOUND);
        }

        return $address;
    }

    private function checkCouponUsageLimit($couponCodes, $userId)
    {
        foreach ($couponCodes as $code) {
            $coupon = Coupon::findByCode($code);

            if (!$coupon) {
                $message = "Coupon '{$code}' does not exist.";
                Log::error($message, ['userId' => $userId]);
                throw new Exception($message, ErrorCode::NOT_FOUND);
            }

            if ($coupon && $coupon->usageLimitReached($userId)) {
                $message = "The coupon code '{$coupon->code}' usage limit has been reached.";
                Log::error($message, ['userId' => $userId]);
                throw new Exception($message, ErrorCode::FORBIDDEN);            }
        }
    }

    public function reduceStock($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $item = $cartItem->product->has_variant ? $cartItem->variant : $cartItem->product;

            $item->decrement('quantity', $cartItem->quantity);

            if ($item->quantity <= 0) {
                $item->markAsOutOfStock();
                $this->notifyOutofStock($item);
            }
        }
    }

    private function sendOrderInvoice($userId, $order)
    {
        $orderData = $this->orderShowService->show($order->id);

        $orderAddress = OrderAddress::where('user_id', $userId)
            ->where('order_id', $order->id)
            ->with('address')
            ->first();

        if (!$orderAddress) {
            throw new Exception('Address not found for related order.', ErrorCode::NOT_FOUND);
        }

        $template = EmailTemplate::where('name', 'order_invoice')->first();

        if (!$template) {
            Log::error('Email template not found.', ['templateName' => 'order_invoice']);
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        $title = strtr($template->title, [
            '{ORDER_NUMBER}' => '#' . $order->id
        ]);

        $subject = strtr($template->subject, [
            '{ORDER_NUMBER}' => '#' . $order->id
        ]);

        $message = strtr($template->message, [
            '{FULLNAME}' => $orderAddress->address->first_name . ' ' . $orderAddress->address->last_name,
        ]);

        $sendOrderInvoiceEmailDTO = SendOrderInvoiceEmailDTO::from([
            'system' => $template->name,
            'title' => $title,
            'subject' => $subject,
            'image' => $template->image,
            'message' => $message,
            'description' => $template->description,
        ]);

        Event::Dispatch(new SendOrderInvoiceMail($orderData, $sendOrderInvoiceEmailDTO));
    }

    public function logOrderCreation(Order $order)
    {
        $description = $order->payment_method === GatewayConstant::COD
         ? "Payment to be made upon delivery. Order status changed from " . Order::$orderStatusMapping[Order::PENDING_PAYMENT] . " to " . Order::$orderStatusMapping[Order::ORDER_PLACED] . "."
         : "Order status changed to " .  Order::$orderStatusMapping[Order::PENDING_PAYMENT] . ".";

        Event::dispatch(
            new OrderLogEvent(
                $description,
                $order->id,
                $modifierId ?? null,
            )
        );
    }

    private function notifyOutofStock($item)
    {
        $admin = AdminUser::role('Super Admin')->first();
        Notification::send($admin, new ProductOutOfStockNotification($item));
    }

    private function notifyLowStock($item)
    {
        $admin = AdminUser::role('Super Admin')->first();
        Notification::send($admin, new ProductLowStockNotification($item));
    }
}

<?php

namespace Modules\Tag\Service;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Product\App\Models\Product;
use Modules\Review\App\Models\Review;
use Modules\Review\App\Models\ReviewReply;
use Modules\Tag\App\Models\Tag;

class TagIndexService
{
    function index($name)
    {
//        $wpDb = DB::connection('wordpress');  // Configure this in config/database.php

        // 1. Migrate Users
//        $wpUsers = $wpDb->table('wp_users')
//            ->join('wp_usermeta', 'wp_users.ID', '=', 'wp_usermeta.user_id')
//            ->where('wp_users.ID', 9065)
//            ->get();
//
//        $billingData = [];
//
//        try {
//            foreach ($wpUsers as $wpUser) {
//        $gravatarUrl = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($wpUser->user_email)));
//                DB::table('users')->insertOrIgnore([
//                    'id' => $wpUser->ID,
//                    'uuid' => Str::uuid(),
//                    'full_name' => $wpUser->display_name,
//                    'email' => $wpUser->user_email,
//                    'profile_picture' => $gravatarUrl . '?s=96&d=retro&r=g',
//                    'password' => $wpUser->user_pass, // Consider rehashing if not using same algorithm
//                    'created_at' => Carbon::parse($wpUser->user_registered),
//                    'updated_at' => Carbon::now(),
//                ]);
//
//                switch ($wpUser->meta_key) {
//                    case 'billing_first_name':
//                        $billingData['first_name'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_last_name':
//                        $billingData['last_name'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_phone':
//                        $billingData['mobile'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_phone2':
//                        $billingData['backup_mobile'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_address_1':
//                        $billingData['address'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_country':
//                        $billingData['country_name'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_province':
//                        $billingData['province_name'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_city':
//                        $billingData['city_name'] = $wpUser->meta_value;
//                        break;
//                    case 'billing_postcode':
//                        $billingData['zone_name'] = $wpUser->meta_value; // If your zones are equivalent to postcode
//                        break;
//                }
//            }
//
//            $billingData['user_id'] = $wpUser->ID;    // WordPress User ID which maps to your Laravel User ID
//            $billingData['uuid'] = Str::uuid()->toString();
//            $billingData['created_at'] = now();
//            $billingData['updated_at'] = now();
//
//            DB::table('addresses')->insert($billingData);
//
//        } catch (\Exception $exception) {
//            dd($exception);
//        }


        // Product reviews and questions
//        $wpProduct = $wpDb->table('wp_posts')
//            ->where('post_type', 'product')
//            ->where('post_status', 'publish')
//            ->where('post_name', 'ultima-atom-192-earbuds')
//            ->select('ID')
//            ->first();
//
//        $product = Product::where('slug', 'ultima-atom-192-earbuds')->first();
//
//        try {
//            $comments = $wpDb->table('wp_comments')
//                ->leftJoin('wp_commentmeta', function ($join) {
//                    $join->on('wp_comments.comment_ID', '=', 'wp_commentmeta.comment_id')
//                        ->where('wp_commentmeta.meta_key', 'rating'); // Filter only rating
//                })->where('wp_comments.comment_post_ID', $wpProduct->ID)
//                ->whereIn('wp_comments.comment_type', ['review', 'question', 'comment'])  // Assuming 'reply' type for replies
//                ->select('wp_comments.*', 'wp_commentmeta.meta_value as rating') // Fetch only rating
//                ->get();
//
////        dd($comments);
//            $groupedComments = [];
//
//            foreach ($comments as $comment) {
//                // Check if it's a top-level comment (either a question or review)
//                if ($comment->comment_parent == 0) {
//                    $groupedComments[$comment->comment_ID] = [
//                        'comment' => $comment,  // Stores the main question or review
//                        'replies' => []         //   Initializes an empty array for replies
//                    ];
//                } else {
//                    // If it's a reply, add it to the corresponding parent comment (question or review)
//                    if (isset($groupedComments[$comment->comment_parent])) {
//                        $groupedComments[$comment->comment_parent]['replies'][] = $comment;
//                    }
//                }
//            }
//
//// Convert back to a collection if you need it as a Collection instance
//            $groupedComments = collect($groupedComments);
//
//            foreach ($groupedComments as $commentID => $groupedComment) {
//                $comment = $groupedComment['comment'];
//                $replies = $groupedComment['replies'];
//
//                // Insert the main comment into the reviews table
//                $review = new Review();
//                $review->uuid = Str::uuid();
//                $review->user_id = $comment->user_id ?: null; // Assuming `user_id` is available or set to null
//                $review->product_id = $product->id;
//                $review->type = $comment->comment_type === 'review' ? 'review' : 'question';
//                $review->name = $comment->comment_author;
//                $review->email = $comment->comment_author_email;
//                $review->rating = $comment->rating ? (int)$comment->rating : null; // Casting rating to integer if available
//                $review->comment = $comment->comment_content;
//                $review->is_approved = $comment->comment_approved === '1'; // Assuming '1' means approved
//                $review->is_replied = !empty($replies); // True if there are replies
//                $review->ip_address = $comment->comment_author_IP;
//
//                // Save the main review record to get the review ID
//                $review->save();
//
//                foreach ($replies as $reply) {
//                    $reviewReply = new ReviewReply();
//                    $reviewReply->review_id = $review->id;
//                    $reviewReply->content = $reply->comment_content;
//
//                    // Save each reply associated with the main review
//                    $reviewReply->save();
//                }
//            }
//        }
//        catch (\Exception $exception) {
//        dd($exception);
//    }


        // Order migration starts

//        $wpOrder = $wpDb->table('wp_posts')
//            ->where('post_type', 'shop_order')
//            ->where('ID', 352)
//            ->first();

//        dd($wpOrders);

//        foreach ($wpOrders as $wpOrder) {

//            $cancelledDate = null;
//
//            if ($wpOrder->post_status === 'wc-cancelled') {
//                $cancelledDate = $wpOrder->post_modified;
//            }
//
//            $order = $wpDb->table('wp_postmeta')
//                ->where('post_id', $wpOrder->ID)
//                ->get();
//
//            $orderTotal = $wpDb->table('wp_postmeta')
//                ->where('post_id', $wpOrder->ID)
//                ->where('meta_key', '_order_total')
//                ->value('meta_value');
//
//            $customerId = $wpDb->table('wp_postmeta')
//                ->where('post_id', $wpOrder->ID)
//                ->where('meta_key', '_customer_user')
//                ->value('meta_value');
//
//            $discount = $wpDb->table('wp_postmeta')
//                ->where('post_id', $wpOrder->ID)
//                ->where('meta_key', '_cart_discount')
//                ->value('meta_value');
//
//            $paymentMethod = $wpDb->table('wp_postmeta')
//                ->where('post_id', $wpOrder->ID)
//                ->where('meta_key', '_payment_method')
//                ->value('meta_value');


//            dd($order, $cancelledDate, $orderTotal, $customerId, $discount, $paymentMethod);
//            $orderId = DB::table('orders')->insertGetId([
//                'id' => $wpOrder->ID,
//                'user_id' => $customerId,
//                'subtotal' => $orderTotal + $discount,
//                'discount' => $discount,
//                'delivery_charge' => 0,
//                'total' => $orderTotal,
//                'note' => '',
//                'payment_method' => $paymentMethod,
//                'cancelled_date' => $cancelledDate,
//                'status' => $this->mapOrderStatus($wpOrder->post_status),
//                'created_at' => Carbon::parse($wpOrder->post_date),
//                'updated_at' => Carbon::parse($wpOrder->post_modified),
//            ]);
//
//            $orderItems = $wpDb->table('wp_woocommerce_order_items')
//                ->where('order_id', $wpOrder->ID)
//                ->get();
//
//            dd($orderItems);
//
//            foreach ($orderItems as $item) {
//                $productId = $wpDb->table('wp_woocommerce_order_itemmeta')
//                    ->where('order_item_id', $item->order_item_id)
//                    ->where('meta_key', '_product_id')
//                    ->value('meta_value');
//
//                $quantity = $wpDb->table('wp_woocommerce_order_itemmeta')
//                    ->where('order_item_id', $item->order_item_id)
//                    ->where('meta_key', '_qty')
//                    ->value('meta_value');
//
//                DB::table('order_items')->insert([
//                    'order_id' => $orderId,
//                    'product_id' => $productId,
//                    'quantity' => $quantity,
//                    'created_at' => Carbon::now(),
//                    'updated_at' => Carbon::now(),
//                ]);
//            }
//        }
//
//        $orderItems = $wpDb->table('wp_woocommerce_order_items')
//            ->where('order_id', 6494)
//            ->get();
//
//        dd($orderItems);



        $query = Tag::query();

        $query->when(isset($name), function ($query) use ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        });

        $result =  $query->latest('created_at')
            ->select('id', 'name', 'slug as url', 'created_at as created')
            ->paginate(25);

        return $result ?? collect([]);
    }

//    private function mapOrderStatus($wpStatus)
//    {
//        $statusMap = [
//            'shipped' => 'shipped',
//            'trash' => 'trash',
//            'wc-pending' => 'pending',
//            'wc-processing' => 'processing',
//            'wc-on-hold' => 'on-hold',
//            'wc-completed' => 'completed',
//            'wc-cancelled' => 'cancelled',
//            'wc-refunded' => 'refunded',
//            'wc-failed' => 'failed'
//        ];
//
//        return $statusMap[$wpStatus] ?? 'pending';
//    }
}

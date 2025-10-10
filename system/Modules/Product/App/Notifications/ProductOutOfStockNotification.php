<?php

namespace Modules\Product\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProductOutOfStockNotification extends Notification
{
    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    // Use only the database channel
    public function via($notifiable)
    {
        return ['database'];
    }

    // Store notification data in the database
    public function toArray($notifiable)
    {
        // Get variant option details from pivot table relationship
        $variantDetails = '';
        if (!$this->product->product_name && $this->product->optionValues) {
            $optionNames = $this->product->optionValues->map(function($optionValue) {
                return $optionValue->option_name;
            })->implode(', ');
            $variantDetails = $optionNames;
        }

        return [
            'product_id' => $this->product->uuid,
            'product_name' => $this->product->product_name ?: $this->product->product->product_name,
            'variant_name' => $this->product->name ?? '', // Example: "Black"
            'message' => $this->product->product_name
                ? "{$this->product->product_name} is out of stock."
                : "The variant '{$variantDetails}' of product '{$this->product->product->product_name}' is currently out of stock."
        ];
    }
}

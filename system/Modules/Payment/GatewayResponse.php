<?php

namespace Modules\Payment;

use JsonSerializable;

abstract class GatewayResponse
{
    abstract public function getOrderId();

    public function toArray()
    {
        $data = ['orderId' => $this->getOrderId()];

        try {
            if ($this instanceof ShouldRedirect) {
                $data['redirectUrl'] = $this->getRedirectUrl();
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $data;
    }
}

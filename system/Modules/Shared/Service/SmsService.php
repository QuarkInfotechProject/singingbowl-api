<?php

namespace Modules\Shared\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Constant\UrlConstant;

class SmsService
{
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $organisationCode;
    protected $http;

    public function __construct()
    {
        $this->apiUrl = UrlConstant::SWIFT_SMS_BASE_URL;
        $this->username = config('services.swift.username');
        $this->password = config('services.swift.password');
        $this->organisationCode = config('services.swift.org_code');

        $this->http = Http::withBasicAuth($this->username, $this->password)
            ->withHeaders(['OrganisationCode' => $this->organisationCode]);
    }

    public function sendSms($phoneNumber, $message)
    {
        try {
            $response = $this->http->post($this->apiUrl, [
                'ReceiverNo' => $phoneNumber,
                'IsClientLogin' => 'N',
                'Message' => $message,
            ]);

            if (!$response->successful()) {
                throw new \Exception('SMS sending failed: ' . $response->body());
            }

            return true;
        } catch (\Exception $exception) {
            Log::error('Failed to send SMS', [
                'error' => $exception->getMessage(),
                'phoneNumber' => $phoneNumber,
            ]);

            return false;
        }
    }
}

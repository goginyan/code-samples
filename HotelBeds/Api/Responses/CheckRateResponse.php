<?php

namespace App\Components\Redemption\HotelBeds\Api\Responses;

class CheckRateResponse
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response['hotel'] ?? null;
    }

    public function failed()
    {
        return $this->response == null || isset($this->response["error_code"]);
    }

    protected function get($key)
    {
        return $this->response[$key] ?? null;
    }

    public function hotelsProCode()
    {
        return $this->get('code');
    }

    public function __toString()
    {
        return (string)$this->hotelsProCode();
    }

    public function totalPrice(){
        return $this->get('totalNet') ?? 0;
    }

    public function currency(){
        return $this->get('currency') ?? 0;
    }

}
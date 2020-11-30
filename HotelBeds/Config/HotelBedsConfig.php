<?php

namespace App\Components\Redemption\HotelBeds\Config;

class HotelBedsConfig
{
    public $apiBaseUrl;
    public $secureApiBaseUrl;
    public $apiHeaderAccept;
    public $apiHeaderAcceptEncoding;
    public $apiKey;
    public $apiSecret;
    public $clientReference;

    /**
     * HotelBedsConfig constructor.
     * @param $apiBaseUrl
     * @param $secureApiBaseUrl
     * @param $apiHeaderAccept
     * @param $apiHeaderAcceptEncoding
     * @param $apiKey
     * @param $apiSecret
     * @param $clientReference
     */
    public function __construct($apiBaseUrl,
                                $secureApiBaseUrl,
                                $apiHeaderAccept,
                                $apiHeaderAcceptEncoding,
                                $apiKey,
                                $apiSecret,
                                $clientReference)
    {

        $this->apiBaseUrl = $apiBaseUrl;
        $this->secureApiBaseUrl = $secureApiBaseUrl;
        $this->apiHeaderAccept = $apiHeaderAccept;
        $this->apiHeaderAcceptEncoding = $apiHeaderAcceptEncoding;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->clientReference = $clientReference;
    }

}
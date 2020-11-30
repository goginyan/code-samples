<?php

namespace App\Components\Redemption\HotelBeds\Config;

use App\Infrastructure\ConfigurationManager\ComponentConfigManager;

class HotelBedsConfigManager
{
    public $configManager;

    /**
     * HotelBedsConfigManager constructor.
     * @param ComponentConfigManager $configManager
     */
    public function __construct(ComponentConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return HotelBedsConfig
     */
    public function getConfig()
    {
        $config = $this->configManager->config('hotelbeds');
        return new HotelBedsConfig(
            $config['api_base_url'],
            $config['secure_api_base_url'],
            $config['api_header_accept'],
            $config['api_header_accept_encoding'],
            $config['api_key'],
            $config['api_secret'],
            $config['client_reference']
        );
    }

}

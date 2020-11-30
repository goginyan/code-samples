<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Money\Money;
use App\Services\PointConverter;

class HotelBedsPriceCalculator
{
    /**
     * @var \App\Services\PointConverter
     */
    private $pointConverter;

    /**
     * HotelBedsPriceCalculator constructor.
     * @param PointConverter $pointConverter
     */
    public function __construct(PointConverter $pointConverter)
    {
        $this->pointConverter = $pointConverter;
    }

    /**
     * @param Money $price
     * @return float
     */
    public function price(Money $price)
    {
        return round($this->calculatePrice($price), 2, PHP_ROUND_HALF_UP);
    }

    /**
     * @param $price
     * @return float
     */
    protected function calculatePrice($price)
    {
        return $price * 1.125;
    }

    /**
     * @param $price
     * @param $currency
     * @param $networkId
     * @return float
     */
    public function points($price, $currency, $networkId)
    {
        return $this->pointConverter->toPoints($this->calculatePrice($price), $currency, $networkId);
    }
}
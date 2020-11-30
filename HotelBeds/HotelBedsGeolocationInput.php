<?php

namespace App\Components\Redemption\HotelBeds;

class HotelBedsGeolocationInput
{
    /**
     * @var string
     */
    public $longitude;
    /**
     * @var string
     */
    public $latitude;
    /**
     * @var int|null
     */
    public $radius;
    /**
     * @var string|null
     */
    public $unit;
    /**
     * @var float|null
     */
    public $secondaryLatitude;
    /**
     * @var float|null
     */
    public $secondaryLongitude;

    /**
     * HotelBedsGeolocationInput constructor.
     * @param string $longitude
     * @param string $latitude
     * @param int $radius
     * @param string $unit
     * @param float|null $secondaryLatitude
     * @param float|null $secondaryLongitude
     */
    public function __construct(string $longitude,
                                string $latitude,
                                int $radius,
                                string $unit,
                                ?float $secondaryLatitude,
                                ?float $secondaryLongitude)
    {

        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->radius = $radius;
        $this->unit = $unit;
        $this->secondaryLatitude = $secondaryLatitude;
        $this->secondaryLongitude = $secondaryLongitude;
    }
}
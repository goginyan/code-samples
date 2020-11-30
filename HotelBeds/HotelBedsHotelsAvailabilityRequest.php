<?php

namespace App\Components\Redemption\HotelBeds;

use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class HotelBedsHotelsAvailabilityRequest
{
    /**
     * @var int
     */
    public $networkId;
    /**
     * @var Carbon
     */
    public $checkIn;
    /**
     * @var Carbon
     */
    public $checkOut;
    /**
     * @var int
     */
    public $roomsCount;
    /**
     * @var int
     */
    public $adultsCount;
    /**
     * @var int
     */
    public $childrenCount;
    /**
     * @var int|null
     */
    public $minCategory;
    /**
     * @var int|null
     */
    public $maxCategory;
    public $hotelCodes;
    public $destination;
    /**
     * @var HotelBedsGeolocationInput|null
     */
    public $geolocation;


    /**
     * HotelBedsHotelsAvailabilityRequest constructor.
     * @param int $networkId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @param int $roomsCount
     * @param int $adultsCount
     * @param int $childrenCount
     * @param int|null $minCategory
     * @param int|null $maxCategory
     * @param $hotelCodes
     * @param $destination
     * @param HotelBedsGeolocationInput|null $geolocation
     */
    public function __construct(
        int $networkId,
        Carbon $checkIn,
        Carbon $checkOut,
        int $roomsCount,
        int $adultsCount,
        int $childrenCount,
        ?int $minCategory,
        ?int $maxCategory,
        $hotelCodes,
        $destination,
        ?HotelBedsGeolocationInput $geolocation
    )
    {
        $this->networkId = $networkId;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->roomsCount = $roomsCount;
        $this->adultsCount = $adultsCount;
        $this->childrenCount = $childrenCount;
        $this->minCategory = $minCategory;
        $this->maxCategory = $maxCategory;
        $this->hotelCodes = $hotelCodes;
        $this->destination = $destination;
        $this->geolocation = $geolocation;
    }
}

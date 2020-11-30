<?php

namespace App\Components\Redemption\HotelBeds;

class HotelBedsHotelListItem
{
    public $checkOut;
    public $checkIn;
    public $code;
    public $name;
    public $categoryCode;
    public $categoryName;
    public $destinationCode;
    public $destinationName;
    public $zoneCode;
    public $zoneName;
    public $latitude;
    public $longitude;
    public $minRateInPoints;
    public $maxRateInPoints;
    public $priceInPoints;
    public $searchCode;
    public $address;
    public $accommodationTypeCode;
    public $image;
    public $facilities;
    /**
     * HotelBedsHotelListItem constructor.
     * @param $code
     * @param $name
     * @param $categoryCode
     * @param $categoryName
     * @param $destinationCode
     * @param $destinationName
     * @param $zoneCode
     * @param $zoneName
     * @param $latitude
     * @param $longitude
     * @param $minRateInPoints
     * @param $maxRateInPoints
     * @param $checkOut
     * @param $checkIn
     * @param $searchCode
     * @param $address
     * @param $accommodationTypeCode
     * @param $image
     * @param $facilities
     * @param $stars
     */
    public function __construct($code,
                                $name,
                                $categoryCode,
                                $categoryName,
                                $destinationCode,
                                $destinationName,
                                $zoneCode,
                                $zoneName,
                                $latitude,
                                $longitude,
                                $minRateInPoints,
                                $maxRateInPoints,
                                $checkOut,
                                $checkIn,
                                $searchCode,
                                $address,
                                $accommodationTypeCode,
                                $image,
                                $stars,
                                $facilities
    )
    {
        $this->code = $code;
        $this->name = $name;
        $this->categoryCode = $categoryCode;
        $this->categoryName = $categoryName;
        $this->destinationCode = $destinationCode;
        $this->destinationName = $destinationName;
        $this->zoneCode = $zoneCode;
        $this->zoneName = $zoneName;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->minRateInPoints = $minRateInPoints;
        $this->maxRateInPoints = $maxRateInPoints;
        $this->checkOut = $checkOut;
        $this->checkIn = $checkIn;
        $this->searchCode = $searchCode;
        $this->address = $address;
        $this->accommodationTypeCode = $accommodationTypeCode;
        $this->image = $image;
        $this->stars = $stars;
        $this->facilities = $facilities;
    }

    public $stars;
}

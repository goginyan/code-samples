<?php

namespace App\Components\Redemption\HotelBeds;

use Carbon\Carbon;

class HotelBedsHotelDetail
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

    public $rooms;

    public $priceInPoints;

    public $totalSellingRate;

    public $facilities;

    public $address;

    public $accommodationTypeCode;

    public $images;

    public $stars;

    /**
     * HotelBedsHotelDetail constructor.
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
     * @param $rooms
     * @param Carbon|null $checkOut
     * @param Carbon|null $checkIn
     * @param $facilities
     * @param $address
     * @param $accommodationTypeCode
     * @param $images
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
                                $rooms,
                                ?Carbon $checkOut,
                                ?Carbon $checkIn,
                                $facilities,
                                $address,
                                $accommodationTypeCode,
                                $images,
                                $stars
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
        $this->rooms = $rooms;
        $this->checkOut = $checkOut;
        $this->checkIn = $checkIn;
        $this->facilities = $facilities;
        $this->address = $address;
        $this->accommodationTypeCode = $accommodationTypeCode;
        $this->images = $images;
        $this->stars = $stars;
    }
    /**
     * @return int
     */
    public function totalPrice(){
        return $this->totalNet ?? 0;
    }

    /**
     * @return int
     */
    public function currency(){
        return $this->currency ?? 0;
    }

    /**
     * @return mixed
     */
    public function priceInPoints(){
        $this->priceInPoints = $this->rooms->first()->rates->first()->net->points;

        return $this->priceInPoints;
    }
}

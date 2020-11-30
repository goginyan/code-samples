<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Versioned\Versioned;

class HotelBedsHotelInfo extends  Versioned
{
    protected static $version = 1;

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

    public $currency;

    public $address;

    public $facilities;

    public $accommodationTypeCode;

    public $images;

    public $stars;

    /**
     * HotelBedsHotelInfo constructor.
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
        $this->facilities = $facilities;
        $this->address = $address;
        $this->accommodationTypeCode = $accommodationTypeCode;
        $this->images = $images;
        $this->stars = $stars;
    }

    /**
     * @param $data
     * @return HotelBedsHotelInfo
     */
    public static function fromArray($data)
    {
        return new self(
            $data['code'],
            $data['name'],
            $data['category_code'],
            $data['category_name'],
            $data['destination_code'],
            $data['destination_name'],
            $data['zone_code'],
            $data['zone_name'],
            $data['latitude'],
            $data['longitude'],
            $data['facilities'],
            $data['address'],
            $data['accommodation_type_code'],
            $data['images'],
            $data['stars']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'category_code' => $this->categoryCode,
            'category_name' => $this->categoryName,
            'destination_code' => $this->destinationCode,
            'destination_name' => $this->destinationName,
            'zone_code' => $this->zoneCode,
            'zone_name' => $this->zoneName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'facilities' => $this->facilities,
            'address' => $this->address,
            'accommodation_type_code' => $this->accommodationTypeCode,
            'images' => $this->images,
            'stars' => $this->stars,
        ];
    }

}

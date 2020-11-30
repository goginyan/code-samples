<?php

namespace App\Components\Redemption\HotelBeds;


class HotelBedsHotelStaticInfo
{

    public $address;
    public $facilities;
    public $accommodationType;
    public $imagesArray;
    public $image;
    public $stars;

    /**
     * HotelBedsHotelStaticInfo constructor.
     * @param $address
     * @param $facilities
     * @param $accommodationType
     * @param $imagesArray
     * @param $image
     * @param $stars
     */
    public function __construct($address,
                                $facilities,
                                $accommodationType,
                                $imagesArray,
                                $stars,
                                $image
                                )
    {

        $this->address = $address;
        $this->facilities = $facilities;
        $this->accommodationType = $accommodationType;
        $this->imagesArray = $imagesArray;
        $this->stars = $stars;
        $this->image = $image;
    }


}

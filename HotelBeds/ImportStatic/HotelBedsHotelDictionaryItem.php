<?php

namespace App\Components\Redemption\HotelBeds\ImportStatic;

class HotelBedsHotelDictionaryItem
{

    public $code;
    public $name;
    public $countryCode;
    public $destinationCode;
    public $categoryName;
    public $categoryCode;
    public $description;
    public $stateCode;
    public $zoneCode;
    public $categoryGroupCode;
    public $chainCode;
    public $accommodationTypeCode;
    public $address;
    public $postalCode;
    public $city;
    public $email;
    public $generalImages;
    public $facilityCodes;

    /**
     * HotelBedsHotelDictionaryItem constructor.
     * @param $code
     * @param $name
     * @param $countryCode
     * @param $destinationCode
     * @param $categoryCode
     * @param $description
     * @param $stateCode
     * @param $zoneCode
     * @param $categoryGroupCode
     * @param $chainCode
     * @param $accommodationTypeCode
     * @param $address
     * @param $postalCode
     * @param $city
     * @param $email
     * @param $generalImages
     */
    public function __construct($code,
                                $name,
                                $countryCode,
                                $destinationCode,
                                $categoryCode,
                                $description,
                                $stateCode,
                                $zoneCode,
                                $categoryGroupCode,
                                $chainCode,
                                $accommodationTypeCode,
                                $address,
                                $postalCode,
                                $city,
                                $email,
                                $generalImages
    )
    {

        $this->code = $code;
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->destinationCode = $destinationCode;
        $this->categoryCode = $categoryCode;
        $this->description = $description;
        $this->stateCode = $stateCode;
        $this->zoneCode = $zoneCode;
        $this->categoryGroupCode = $categoryGroupCode;
        $this->chainCode = $chainCode;
        $this->accommodationTypeCode = $accommodationTypeCode;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->email = $email;
        $this->generalImages = $generalImages;
    }

}

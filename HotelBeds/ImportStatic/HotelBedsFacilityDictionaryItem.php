<?php

namespace App\Components\Redemption\HotelBeds\ImportStatic;

class HotelBedsFacilityDictionaryItem
{
    public $code;
    public $description;

    /**
     * HotelBedsFacilityDictionaryItem constructor.
     * @param $code
     * @param $description
     */
    public function __construct($code, $description)
    {
        $this->code = $code;
        $this->description = $description;
    }
}

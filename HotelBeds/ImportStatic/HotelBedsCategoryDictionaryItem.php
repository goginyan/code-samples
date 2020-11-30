<?php

namespace App\Components\Redemption\HotelBeds\ImportStatic;

class HotelBedsCategoryDictionaryItem
{
    public $code;
    public $simpleCode;

    /**
     * HotelBedsCategoryDictionaryItem constructor.
     * @param $code
     * @param $simpleCode
     */
    public function __construct($code, $simpleCode)
    {
        $this->code = $code;
        $this->simpleCode = $simpleCode;
    }
}

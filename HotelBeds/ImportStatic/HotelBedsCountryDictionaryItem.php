<?php

namespace App\Components\Redemption\HotelBeds\ImportStatic;

class HotelBedsCountryDictionaryItem
{
    public $code;
    public $name;

    /**
     * HotelBedsCountryDictionaryItem constructor.
     * @param $code
     * @param $name
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }
}

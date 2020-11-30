<?php

namespace App\Components\Redemption\HotelBeds\ImportStatic;

class HotelBedsDestinationDictionaryItem
{


    public $code;
    public $name;
    public $countryCode;
    public $isoCode;

    /**
     * HotelBedsDestinationDictionaryItem constructor.
     * @param $code
     * @param $name
     * @param $countryCode
     * @param $isoCode
     */
    public function __construct($code,
                                $name,
                                $countryCode,
                                $isoCode = null
                                )
    {

        $this->code = $code;
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->isoCode = $isoCode;
    }

}

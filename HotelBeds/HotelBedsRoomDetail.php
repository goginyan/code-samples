<?php

namespace App\Components\Redemption\HotelBeds;


class HotelBedsRoomDetail
{
    public $code;
    public $name;
    /**
     * @var Collection|HotelBedsRate[]
     */
    public $rates;

    public function __construct($code,
                                $name,
                                $rates)
    {
        $this->code = $code;
        $this->name = $name;
        $this->rates = $rates;

    }
}

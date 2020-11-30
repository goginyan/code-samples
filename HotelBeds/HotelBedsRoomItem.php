<?php

namespace App\Components\Redemption\HotelBeds;


class HotelBedsRoomItem
{
    public $code;
    public $name;
    /**
     * @var HotelBedsRate
     */
    public $rate;

    /**
     * HotelBedsRoomItem constructor.
     * @param $code
     * @param $name
     * @param HotelBedsRate $rate
     */
    public function __construct($code,
                                $name,
                                HotelBedsRate $rate)
    {
        $this->code = $code;
        $this->name = $name;
        $this->rate = $rate;

    }
}

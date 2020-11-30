<?php

namespace App\Components\Redemption\HotelBeds;

class HotelBedsRoomOffer
{
    public $hotelInfo;

    public $roomItem;


    /**
     * HotelBedsRoomOffer constructor.
     * @param HotelBedsHotelInfo $hotelInfo
     * @param HotelBedsRoomItem $roomItem
     */
    public function __construct(HotelBedsHotelInfo $hotelInfo,
                                HotelBedsRoomItem  $roomItem
                                )
    {
        $this->hotelInfo = $hotelInfo;
        $this->roomItem  = $roomItem;
    }
}

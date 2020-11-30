<?php

namespace App\Components\Redemption\HotelBeds;


use App\Infrastructure\Versioned\Versioned;

class HotelBedsOrderItem extends Versioned
{
    protected static $version = 1;
    public $hotelInfo;
    public $offerInfo;

    /**
     * HotelBedsOrderItem constructor.
     * @param HotelBedsHotelInfo $hotelInfo
     * @param HotelBedsOfferItem $offerInfo
     */
    public function __construct(HotelBedsHotelInfo $hotelInfo,
                                HotelBedsOfferItem $offerInfo
                                )
    {
        $this->hotelInfo = $hotelInfo;
        $this->offerInfo = $offerInfo;

    }

    /**
     * @param $data
     * @return HotelBedsOrderItem
     */
    public static function fromArray($data)
    {
        return new self(
            HotelBedsHotelInfo::fromVersionedArray($data['hotel_info']),
            HotelBedsOfferItem::fromVersionedArray($data['offer_info'])
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'hotel_info' => $this->hotelInfo,
            'offer_info' => $this->offerInfo,
        ];

    }
}

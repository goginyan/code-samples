<?php

namespace App\Components\Redemption\HotelBeds\Projector;

use App\Components\Redemption\Hotel\RoomOfOfferItem;
use Illuminate\Database\Eloquent\Model;

class HotelRoomProjectorModel extends Model
{
    public $table = 'transaction_hotel_room';

    protected $guarded = [];

    /**
     * @param $transactionHotelId
     * @param RoomOfOfferItem $room
     * @return HotelRoomProjectorModel
     */
    public static function createRoom($transactionHotelId, RoomOfOfferItem $room): HotelRoomProjectorModel
    {
        return self::create([
            'trx_hotel_id' => $transactionHotelId,
            'category' => $room->category,
            'description' => $room->description,
            'type' => $room->type,
        ]);
    }
}

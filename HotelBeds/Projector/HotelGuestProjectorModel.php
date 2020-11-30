<?php

namespace App\Components\Redemption\HotelBeds\Projector;

use App\Components\Redemption\Hotel\HotelVisitor;
use Illuminate\Database\Eloquent\Model;

class HotelGuestProjectorModel extends Model
{
    public $table = 'transaction_hotel_guest';

    protected $guarded = [];

    /**
     * @param $transactionHotelId
     * @param HotelVisitor $visitor
     * @return HotelGuestProjectorModel
     */
    public static function createGuest($transactionHotelId, HotelVisitor $visitor): HotelGuestProjectorModel
    {
        return self::create([
            'trx_hotel_id' => $transactionHotelId,
            'guest_first_name' => $visitor->firstName,
            'guest_last_name' => $visitor->lastName,
            'guest_age' => $visitor->age,
        ]);
    }
}

<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Versioned\Versioned;
use Illuminate\Support\Collection;

class HotelBedsOrderInput extends Versioned
{
    protected static $version = 1;
    public $rateKey;
    public $holderFirstName;
    public $holderLastName;
    /**
     * @var HotelBedsRoomInput[]|Collection
     */
    public $rooms;
    public $remark;

    /**
     * HotelBedsOrderInput constructor.
     * @param $rateKey
     * @param $holderFirstName
     * @param $holderLastName
     * @param $rooms
     * @param $remark
     */
    public function __construct($rateKey,$holderFirstName, $holderLastName,  $rooms,   $remark )
    {
        $this->rateKey = $rateKey;
        $this->holderFirstName = $holderFirstName;
        $this->holderLastName = $holderLastName;
        $this->rooms = $rooms;
        $this->remark = $remark;
;
    }

    /**
     * @param $data
     * @return HotelBedsOrderInput|null
     */
    public static function fromArray($data)
    {
        return new self(
            $data['rate_key'],
            $data['holder_first_name'],
            $data['holder_last_name'],
            collect($data['rooms'])->map(function ($room) {
                return HotelBedsRoomInput::fromVersionedArray($room);
            }),
            $data['remark']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'rate_key' => $this->rateKey,
            'holder_first_name' => $this->holderFirstName,
            'holder_last_name' => $this->holderLastName,
            'rooms' => $this->rooms,
            'remark' => $this->remark,
        ];
    }


}

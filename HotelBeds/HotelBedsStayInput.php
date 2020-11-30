<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\Order\OrderInput;

class HotelBedsStayInput extends OrderInput
{
    public $checkIn;
    public $checkOut;

    /**
     * HotelBedsStayInput constructor.
     * @param $checkIn
     * @param $checkOut
     */
    public function __construct($checkIn, $checkOut)
    {
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
    }

    /**
     * @param $data
     * @return HotelBedsStayInput|null
     */
    public static function fromArray($data)
    {
        if (!$data) {
            return null;
        }

        return new self(
            $data['check_in'] ?? null,
            $data['check_out'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'checkIn' => $this->checkIn,
            'checkOut' => $this->checkOut,
        ];
    }

}

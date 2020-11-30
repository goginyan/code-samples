<?php

namespace App\Components\Redemption\HotelBeds\VO;

use App\Components\Redemption\HotelBeds\HotelBedsRoomDetail;
use Illuminate\Support\Collection;

class HotelBedsSearchCode
{
    /**
     * @var string
     */
    private $searchCode;
    public $rateKeys;

    /**
     * HotelBedsSearchCode constructor.
     * @param string $searchCode
     */
    public function __construct(string $searchCode)
    {
        $this->searchCode = $searchCode;

        $searchCode = str_replace(' ', '+', $searchCode);
        $this->rateKeys = explode("{!}", gzuncompress(base64_decode($searchCode)));
    }

    /**
     * @param Collection|HotelBedsRoomDetail[] $rooms
     * @return string
     */
    public static function create(Collection $rooms)
    {
        $rateKeysArray = [];

        foreach ($rooms as $room) {
            foreach ($room->rates as $rate) {
                $rateKeysArray[] = $rate->rateKey;
            }
        }

        return new self(base64_encode(gzcompress(implode('{!}', $rateKeysArray), 9)));
    }

    public function __toString(): string
    {
        return (string) $this->searchCode;
    }
}

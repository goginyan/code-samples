<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\Order\OrderInput;

class HotelBedsCheckRatesRequest extends OrderInput
{

    public $rateKey;

    /**
     * HotelBedsCheckRatesInput constructor.
     * @param string $rateKey
     */
    public function __construct(string $rateKey)
    {
        $this->rateKey = $rateKey;
    }

    /**
     * @param $data
     *
     * @return HotelBedsCheckRatesRequest|null
     */
    public static function fromArray($data)
    {
        if (!$data['rate_key']) {
            return null;
        }

        return new self(
            $data['rate_key']
        );
    }

    /**
     * @return \string[][][]
     */
    public function toArray()
    {
        return [
            'rooms' => [
                [
                    'rateKey' => $this->rateKey
                ]
            ]
        ];
    }

}

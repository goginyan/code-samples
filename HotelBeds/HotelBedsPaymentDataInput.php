<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\Order\OrderInput;
use App\Infrastructure\ConfigurationManager\ComponentConfigManager;
use App\User;

class HotelBedsPaymentDataInput extends OrderInput
{
    public $paymentCard;

    public $contactData;

    /**
     * HotelBedsPaymentDataInput constructor.
     * @param $config
     * @param User $user
     */
    public function __construct($config, User $user)
    {
       $this->paymentCard = new HotelBedsPaymentCardInput($config);
       $this->contactData = new HotelBedsContactDataInput($user);
    }

    /**
     * @param $data
     * @return HotelBedsPaymentDataInput|null
     */
    public static function fromArray($data)
    {
        if (!$data) {
            return null;
        }

        return new self((
            new ComponentConfigManager())->config('hotelbeds'),
            $data['user'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'paymentData'=> $this->paymentCard->toArray() + $this->contactData->toArray()
        ];
    }


}

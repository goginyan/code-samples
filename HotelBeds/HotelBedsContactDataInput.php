<?php

namespace App\Components\Redemption\HotelBeds;


use App\Infrastructure\ConfigurationManager\ComponentConfigManager;
use App\User;

class HotelBedsContactDataInput
{
    public $email;
    public $phoneNumber;

    /**
     * HotelBedsContactDataInput constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email = $user->email;
        $this->phoneNumber = $user->normalized_mobile;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'contactData' =>[
                'email'=>$this->email,
                'phoneNumber'=>$this->phoneNumber,
            ]
    ];
    }

}

<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Versioned\Versioned;

class HotelBedsPaxInput extends Versioned
{

    protected static $version = 1;
    public $firstName;
    public $lastName;
    public $type;

    /**
     * HotelBedsPaxInput constructor.
     * @param $firstName
     * @param $lastName
     * @param $type
     */
    public function __construct($firstName, $lastName, $type)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->type = $type;
    }

    /**
     * @param $data
     * @return HotelBedsPaxInput|null
     */
    public static function fromArray($data)
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['type']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'type' => $this->type,
        ];
    }

}

<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Versioned\Versioned;

class HotelBeadsVisitor extends Versioned
{
    protected static $version = 1;

    public $name;
    public $surname;
    public $roomId;

    /**
     * HotelBeadsVisitor constructor.
     * @param $roomId
     * @param $name
     * @param $surname
     */
    public function __construct($roomId, $name, $surname)
    {
        $this->roomId = $roomId;
        $this->name = $name;
        $this->surname = $surname;
    }

    /**
     * @param $data
     * @return HotelBeadsVisitor|null
     */
    public static function fromArray($data)
    {
        if (!$data) {
            return null;
        }

        return new self(
            $data['room_id'] ?? null,
            $data['name'] ?? null,
            $data['surname'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'roomId' => $this->roomId,
            'name'  => $this->name,
            'surname'   => $this->surname
        ];
    }
}
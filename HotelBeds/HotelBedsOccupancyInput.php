<?php

namespace App\Components\Redemption\HotelBeds;

class HotelBedsOccupancyInput
{
    public $rooms;
    public $adults;
    public $children;

    /**
     * HotelBedsOccupancyInput constructor.
     * @param $rooms
     * @param $adults
     * @param $children
     */
    public function __construct($rooms, $adults, $children)
    {
        $this->rooms = $rooms;
        $this->adults = $adults;
        $this->children = $children;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'rooms' => $this->rooms,
            'adults' => $this->adults,
            'children' => $this->children
        ];
    }

}

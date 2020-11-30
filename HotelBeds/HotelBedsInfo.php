<?php

namespace App\Components\Redemption\HotelBeds;

use Illuminate\Support\Collection;

class HotelBedsInfo implements \JsonSerializable
{
    public $code;
    public $name;
    public $categoryName;

    public function __construct($code, $name,  $categoryName)
    {
        $this->code = $code;
        $this->name = $name;
        $this->categoryName = $categoryName;

    }

    public static function fromArray($data)
    {
        return new HotelBedsInfo(
            $data['code'],
            $data['name'],
            $data['categoryName']
        );
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'categoryName' => $this->categoryName,
        ];
    }

}

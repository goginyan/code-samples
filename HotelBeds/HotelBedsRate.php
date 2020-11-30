<?php

namespace App\Components\Redemption\HotelBeds;


class HotelBedsRate
{

    public $rateKey;
    public $rateClass;
    public $net;
    public $sellingRate;
    public $hotelMandatory;
    public $allotment;
    public $paymentType;
    public $packaging;
    public $boardCode;
    public $boardName;
    public $rooms;
    public $adults;
    public $children;
    public $checkIn;
    public $checkOut;
    public $cancellationPolicies;

    public function __construct($rateKey,
                                $rateClass,
                                $net,
                                $sellingRate,
                                $hotelMandatory,
                                $allotment,
                                $paymentType,
                                $packaging,
                                $boardCode,
                                $boardName,
                                $rooms,
                                $adults,
                                $children,
                                $checkIn,
                                $checkOut,
                                $cancellationPolicies)
    {
        $this->rateKey = $rateKey;
        $this->rateClass = $rateClass;
        $this->net = $net;
        $this->sellingRate = $sellingRate;
        $this->hotelMandatory = $hotelMandatory;
        $this->allotment = $allotment;
        $this->paymentType = $paymentType;
        $this->packaging = $packaging;
        $this->boardCode = $boardCode;
        $this->boardName = $boardName;
        $this->rooms = $rooms;
        $this->adults = $adults;
        $this->children = $children;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->cancellationPolicies = $cancellationPolicies;
    }
}

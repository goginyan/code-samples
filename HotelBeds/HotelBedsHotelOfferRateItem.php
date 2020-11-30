<?php

namespace App\Components\Redemption\HotelBeds;


class HotelBedsHotelOfferRateItem
{

    public $rateClass;
    public $net;
    public $paymentType;
    public $packaging;
    public $boardCode;
    public $boardName;
    public $rooms;
    public $adults;
    public $children;
    public $cancellationPolicies;

    public function __construct($rateClass,
                                $net,
                                $sellingRate,
                                $paymentType,
                                $packaging,
                                $boardCode,
                                $boardName,
                                $rooms,
                                $adults,
                                $children,
                                $cancellationPolicies)
    {
        $this->rateClass = $rateClass;
        $this->net = $net;
        $this->sellingRate = $sellingRate;
        $this->paymentType = $paymentType;
        $this->packaging = $packaging;
        $this->boardCode = $boardCode;
        $this->boardName = $boardName;
        $this->rooms = $rooms;
        $this->adults = $adults;
        $this->children = $children;
        $this->cancellationPolicies = $cancellationPolicies;
    }
}

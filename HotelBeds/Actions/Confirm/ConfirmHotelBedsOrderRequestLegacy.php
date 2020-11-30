<?php

namespace App\Components\Redemption\HotelBeds\Actions\Confirm;

use App\Components\Redemption\Hotel\HotelOrderInput;
use App\Components\Redemption\HotelBeds\HotelBedsOrderInput;
use App\Services\Auth\UserContext;

class ConfirmHotelBedsOrderRequestLegacy
{
    /**
     * @var \App\Services\Auth\UserContext
     */
    public $context;
    public $reservationCode;
    /**
     * @var HotelOrderInput
     */
    public $orderInput;
    /**
     * @var int
     */
    public $pointsToPay;
    /**
     * @var int
     */
    public $payInCash;

    /**
     * ConfirmHotelBedsOrderRequestLegacy constructor.
     * @param UserContext $context
     * @param HotelBedsOrderInput $orderInput
     * @param int $pointsToPay
     * @param int $payInCash
     */
    public function __construct(UserContext $context, HotelBedsOrderInput $orderInput, $pointsToPay = 0, $payInCash = 0)
    {
        $this->context = $context;
        $this->orderInput = $orderInput;
        $this->pointsToPay = $pointsToPay;
        $this->payInCash = $payInCash;
    }
}

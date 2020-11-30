<?php

namespace App\Components\Redemption\HotelBeds\RedemptionTransaction;

use App\Components\Redemption\Hotel\Projector\HotelOrderProjector;
use App\Components\Redemption\HotelBeds\Projector\HotelBedsOrderProjector;
use App\Components\Redemption\HotelBeds\HotelBedsOrderInput;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Components\Redemption\RedemptionReference;
use App\Components\Redemption\RedemptionSource;
use App\Components\Redemption\Transactions\CreateRedemptionTransactionRequest;
use App\Components\Redemption\Transactions\LegacyRedemptionTransactionService;
use App\Services\Auth\UserContext;

class HotelBedsRedemptionService
{
    /**
     * @var LegacyRedemptionTransactionService
     */
    private $itemTransactionService;
    /**
     * @var HotelOrderProjector
     */
    private $projector;

    /**
     * HotelBedsRedemptionService constructor.
     * @param LegacyRedemptionTransactionService $itemTransactionService
     * @param HotelBedsOrderProjector $projector
     */
    public function __construct(LegacyRedemptionTransactionService $itemTransactionService, HotelBedsOrderProjector $projector)
    {
        $this->itemTransactionService = $itemTransactionService;
        $this->projector = $projector;
    }

    /**
     * @param UserContext $context
     * @param InvoiceAmount $invoice
     * @param HotelBedsOrderInput $orderInput
     * @param null $hotelReservationResponse
     * @return mixed|null
     */
    public function redeem(UserContext $context, InvoiceAmount $invoice, HotelBedsOrderInput $orderInput,  $hotelReservationResponse = null)
    {
        $id = $this->itemTransactionService->create(
            new CreateRedemptionTransactionRequest
            (
                $context,
                $invoice->priceInPoints,
                $items = [],
                $source = RedemptionSource::ONLINE_CATALOG,
                $reference = RedemptionReference::BLU_TRAVEL_HOTEL,
                $orderInput->countryId ?? $context->countryId,
                $areaId = null,
                $cityId = null,
                $address1 = null,
                $address2 = null,
                $postalCode = null,
                $email = null,
                $pointsInCash = $invoice->pointsInCash,
                $cashPayment = $invoice->payInCashUsd,
                $notes = json_encode(
                    new HotelBedsRedemptionTransactionType(
                        $invoice,
                        $orderInput,
                        $hotelReservationResponse,
                        null
                    )
                )
            )
            , LegacyRedemptionTransactionService::CLASS_HOTEL
        );


        return $id;
    }
}

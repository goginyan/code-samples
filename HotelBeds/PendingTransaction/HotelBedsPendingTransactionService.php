<?php

namespace App\Components\Redemption\HotelBeds\PendingTransaction;

use App\Components\Redemption\HotelBeds\Api\Responses\CheckRateResponse;
use App\Components\Redemption\HotelBeds\HotelBedsOrderInput;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Components\Redemption\Order\PendingTransaction\CashTransactionEntity;
use App\Components\Redemption\Order\PendingTransaction\CashTransactionStatus;
use App\Components\Redemption\Item\PendingTransaction\PendingTransactionItemType;
use App\Services\Auth\UserContext;

class HotelBedsPendingTransactionService
{
    /**
     * @param UserContext $context
     * @param InvoiceAmount $invoice
     * @param CheckRateResponse $hotelOffer
     * @param HotelBedsOrderInput $orderInput
     * @return CashTransactionEntity
     */
    public function create(UserContext $context, InvoiceAmount $invoice,   CheckRateResponse $hotelOffer, HotelBedsOrderInput $orderInput)
    {
        //we should create here with ::create method and write event
        $transaction = new CashTransactionEntity;

        $transaction->id = null;
        $transaction->user_id = $context->userId;
        $transaction->type = PendingTransactionItemType::HOTEL;
        $transaction->status = CashTransactionStatus::PENDING;

        $transaction->serialized_data = json_encode(
            new PendingTransactionHotelBedsType(
                $context,
                $invoice,
                $hotelOffer,
                $orderInput
            )
        );

        $transaction->save();

        return $transaction;
    }
}

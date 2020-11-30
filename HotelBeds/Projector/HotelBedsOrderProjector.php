<?php

namespace App\Components\Redemption\HotelBeds\Projector;

use App\Components\Redemption\HotelBeds\Order\HotelBedsOrder;
use App\Components\Redemption\HotelBeds\RedemptionTransaction\HotelBedsRedemptionTransaction;
use App\Components\Redemption\Order\OrderType;
use App\Components\Redemption\Order\Projector\OrderProjectorModel;

class HotelBedsOrderProjector
{

    /**
     * @param HotelBedsOrder $order
     */
    public function projectOrder(HotelBedsOrder $order)
    {
        HotelBedsProjectorModel::createHotel(
            $order->id,
            $order->pointsTransactionId,
            $order->hotelInfo,
            $order->hotelOffer,
            $order->invoice,
            $order->input,
            $order->reserveResponse
        );

        HotelBedsRedemptionOrderProjectorModel::createRedemptionOrder(
            $order->hotelInfo,
            $order->hotelOffer,
            $order->context,
            $order->invoice,
            $order->input
        );

        OrderProjectorModel::createOrder(
            $order->id,
            $order->processingStatus,
            OrderType::HOTEL,
            $order->invoice,
            $order->date ?? null
        );
    }

    /**
     * @param $transactionId
     */
    public function projectTransaction($transactionId)
    {
        /** @var $redemptionTransaction HotelBedsRedemptionTransaction */
        $redemptionTransaction = HotelBedsRedemptionTransaction::createFromTransactionId($transactionId);

        HotelBedsProjectorModel::createHotel(
            null,
            $redemptionTransaction->id(),
            $redemptionTransaction->transactionType->hotelInfo,
            $redemptionTransaction->transactionType->hotelOffer,
            $invoice = $redemptionTransaction->transactionType->invoice,
            $orderInput = $redemptionTransaction->transactionType->orderInput,
            $reservationResponse = $redemptionTransaction->transactionType->reserveResponse
        );
    }
}

<?php

namespace App\Components\Redemption\HotelBeds\Projector;

use App\Components\Redemption\Hotel\HotelInfo;
use App\Components\Redemption\Hotel\HotelOfferItem;
use App\Components\Redemption\Hotel\HotelOrderInput;
use App\Components\Redemption\Hotel\HotelVisitor;
use App\Components\Redemption\Hotel\RedemptionTransaction\HotelRedemptionTransaction;
use App\Components\Redemption\Hotel\Responses\HotelApiServiceReserveResponse;
use App\Components\Redemption\Hotel\RoomOfOfferItem;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class HotelBedsProjectorModel extends Model
{
    public $table = 'transaction_hotel';

    protected $guarded = [];

    public static function createHotel($id, $transactionId,  $hotelInfo,  $hotelOffer, InvoiceAmount $invoice,  $orderInput,  $reservationResponse): HotelBedsProjectorModel
    {
        $hotel = self::create([
            'order_id'          => $id,
            'trx_id'            => $transactionId,
            'hotel_code'        => $hotelInfo->code,
            'original_price'    => $hotelOffer->originalPrice,
            'original_currency' => $hotelOffer->originalCurrency,
            'calculated_price'  => $hotelOffer->calculatedPrice,
            'image'             => $hotelInfo->image(),

            'price_in_points' => $invoice->priceInPoints,
            'pay_in_points'   => $invoice->payInPoints,
            'pay_in_cash'     => $invoice->payInCash,
            'pay_in_cash_usd' => $invoice->payInCashUsd,
            'currency_id'     => $invoice->currencyId,
            'currency_code'   => $invoice->currencyCode,
            'points_in_cash'  => $invoice->pointsInCash,

            'adult_quantity' => $orderInput->visitorsAdults->count(),

            'is_refundable'               => $hotelOffer->isRefundable,
            'meal_type'                   => $hotelOffer->mealType,
            'checkin_date'                => $hotelOffer->checkinDate,
            'checkout_date'               => $hotelOffer->checkoutDate,
            'cancellation_policies'       => $hotelOffer->cancellationPolicies,
            'additional_info'             => $hotelOffer->additionalInfo,
            'confirmation_number'         => $reservationResponse->customerConfirmationNumber ?? '',
            'partner_confirmation_number' => $reservationResponse->partnerConfirmationNumber ?? '',
        ]);

        $hotelOffer->rooms->each(function (RoomOfOfferItem $room) use ($hotel) {
           return HotelRoomProjectorModel::createRoom($hotel->id, $room);
        });
        $orderInput->allVisitors()->each(function (HotelVisitor $room) use ($hotel) {
            return HotelGuestProjectorModel::createGuest($hotel->id, $room);
        });

        return $hotel;
    }

    public static function updateStatusByTransactionId($transactionId, $status)
    {
        // todo enable after ading status column
//        return self::where('trx_id', $transactionId)
//            ->update(['status' => $status]);
    }
}

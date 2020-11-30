<?php

namespace App\Components\Redemption\HotelBeds\Projector;

use App\Components\Redemption\Hotel\HotelInfo;
use App\Components\Redemption\Hotel\HotelOfferItem;
use App\Components\Redemption\Hotel\HotelOrderInput;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Services\Auth\UserContext;
use App\Services\RedemptionOrder\RedemptionOrderTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HotelBedsRedemptionOrderProjectorModel extends Model
{
    public $table = 'redemption_order';

    protected $guarded = [];

    /**
     * @param HotelInfo $hotelInfo
     * @param HotelOfferItem $hotelOffer
     * @param UserContext $context
     * @param InvoiceAmount $invoice
     * @param HotelOrderInput $orderInput
     * @return mixed
     */
    public static function createRedemptionOrder(HotelInfo $hotelInfo, HotelOfferItem $hotelOffer, UserContext $context, InvoiceAmount $invoice, HotelOrderInput $orderInput)
    {
        return self::create([

            'full_price_in_usd' => $invoice->priceInUsd($context->networkId),
            'user_id'           => $context->userId,
            'type'              => RedemptionOrderTypes::TRAVEL,

            'cost'                  => $hotelOffer->originalPrice,
            'cost_currency'         => $hotelOffer->originalCurrency,
            'cash_payment'          => $invoice->payInCash,
            'cash_payment_currency' => $invoice->currencyId,

            'passenger_name'     => $orderInput->fullName(),
            'company'            => $hotelInfo->name,
            'start_booking_date' => $hotelOffer->checkinDate,
            'end_booking_date'   => $hotelOffer->checkoutDate,

            'order_date' => Carbon::now(),
        ]);
    }
}

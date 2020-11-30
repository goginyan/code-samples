<?php

namespace App\Components\Redemption\HotelBeds\RedemptionTransaction;

use App\Components\Redemption\RedemptionTransaction\RedemptionTransaction;

class HotelBedsRedemptionTransaction extends RedemptionTransaction
{
    /**
     * @var \App\Components\Redemption\Hotel\RedemptionTransaction\HotelRedemptionTransactionType
     */
    public $transactionType;

    /**
     * HotelBedsRedemptionTransaction constructor.
     * @param $id
     * @param $userId
     * @param $partnerId
     * @param $networkId
     * @param $storeId
     * @param $posId
     * @param $pointsBalance
     * @param $pointsRedeemed
     * @param $amtRedeem
     * @param $cashPayment
     * @param $totalAmount
     * @param $reversed
     * @param $originalTotalAmount
     * @param $partnerAmount
     * @param $partnerCurrencyId
     * @param $currencyId
     * @param $exchangeRate
     * @param $countryId
     * @param $areaId
     * @param $cityId
     * @param $createdAt
     * @param $updatedAt
     * @param HotelBedsRedemptionTransactionType $transactionType
     */
    public function __construct(
        $id, $userId, $partnerId, $networkId, $storeId, $posId, $pointsBalance, $pointsRedeemed, $amtRedeem, $cashPayment, $totalAmount,
        $reversed, $originalTotalAmount, $partnerAmount, $partnerCurrencyId, $currencyId, $exchangeRate, $countryId, $areaId, $cityId, $createdAt, $updatedAt,
        HotelBedsRedemptionTransactionType $transactionType)
    {
        parent::__construct($id, $userId, $partnerId, $networkId, $storeId, $posId, $pointsBalance, $pointsRedeemed, $amtRedeem, $cashPayment, $totalAmount,
            $reversed, $originalTotalAmount, $partnerAmount, $partnerCurrencyId, $currencyId, $exchangeRate, $countryId, $areaId, $cityId, $createdAt, $updatedAt
        );

        $this->transactionType = $transactionType;
    }

    /**
     * @param $transaction
     * @return HotelBedsRedemptionTransaction
     */
    public static function createFromEntity($transaction)
    {
         return   new self(
                $transaction->id,
                $transaction->user_id,
                $transaction->partner_id,
                $transaction->network_id,
                $transaction->store_id,
                $transaction->pos_id,
                $transaction->points_balance,
                $transaction->points_redeemed,
                $transaction->amt_redeem,
                $transaction->cash_payment,
                $transaction->total_amount,
                $transaction->reversed,
                $transaction->original_total_amount,
                $transaction->partner_amount,
                $transaction->partner_currency_id,
                $transaction->currency_id,
                $transaction->exchange_rate,
                $transaction->country_id,
                $transaction->area_id,
                $transaction->city_id,
                $transaction->created_at,
                $transaction->updated_at,
                HotelBedsRedemptionTransactionType::fromArray(json_decode($transaction->notes, true))
            );

    }

    /**
     * @return \App\Components\Redemption\Hotel\HotelOfferItem
     */
    public function hotelOfferItem()
    {
        return $this->transactionType->hotelOffer;
    }

    /**
     * @return \App\Components\Redemption\Hotel\HotelInfo
     */
    public function hotelInfo()
    {
        return $this->transactionType->hotelInfo;
    }

    /**
     * @return \App\Components\Redemption\Hotel\HotelOrderInput
     */
    public function orderInput()
    {
        return $this->transactionType->orderInput;
    }

    /**
     * @return \App\Components\Redemption\Hotel\Responses\HotelApiServiceReserveResponse|null
     */
    public function reservation()
    {
        return $this->transactionType->reserveResponse;
    }

    /**
     * @param $transaction
     * @return bool
     */
    public static function entityIsValid($transaction)
    {
        $jsonNotes = json_decode($transaction->notes);
        return isset($jsonNotes->type) && str_contains($jsonNotes->type, 'HotelBedsRedemptionTransactionType');
    }
}

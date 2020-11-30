<?php

namespace App\Components\Redemption\HotelBeds\Order;

use App\Components\Redemption\Car\Responses\CarCancelReservationResponse;
use App\Components\Redemption\Config\RedemptionConfigManager;
use App\Components\Redemption\Hotel\HotelInfo;
use App\Components\Redemption\Hotel\HotelOfferItem;
use App\Components\Redemption\Hotel\HotelOrderInput;
use App\Components\Redemption\Hotel\HotelPriceCalculator;
use App\Components\Redemption\Hotel\HotelPro\HpHotelService;
use App\Components\Redemption\Hotel\Responses\HotelApiServiceReserveResponse;
use App\Components\Redemption\HotelBeds\HotelBedsOrderInput;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Components\Redemption\Order\OrderType;
use App\Components\Redemption\Order\OrderBase;
use App\Components\Redemption\Order\OrderId;
use App\Domain\VO\Points;
use App\Services\Auth\UserContext;
use App\Services\TransClass;
use Illuminate\Support\Facades\Log;

class HotelBedsOrder extends OrderBase
{
    /**
     * @var HotelBedsOrderInput
     */
    public $input;
    /**
     * @var HotelOfferItem
     */
    public $hotelOffer;
    /**
     * @var HotelInfo
     */
    public $hotelInfo;
    /**
     * @var null
     */
    public $reserveResponse;
    /**
     * @var CarCancelReservationResponse|null
     */
    public $cancellationResponse;

    /**
     * Order constructor.
     * @param OrderId $id
     * @param UserContext $context
     * @param InvoiceAmount $invoice
     * @param HotelOrderInput $orderInput
     * @param HotelOfferItem $hotelOffer
     * @param HotelInfo $hotelInfo
     * @param null $reserveResponse
     * @param CarCancelReservationResponse|null $cancellationResponse
     */
    public function __construct(OrderId $id,
                                UserContext $context,
                                InvoiceAmount $invoice,
                                HotelOrderInput $orderInput,
                                HotelOfferItem $hotelOffer,
                                HotelInfo $hotelInfo,
                                $reserveResponse = null,
                                $cancellationResponse = null)
    {
        parent::__construct($id, $context, $invoice);
        $this->input      = $orderInput;
        $this->hotelOffer = $hotelOffer;
        $this->hotelInfo = $hotelInfo;
        $this->reserveResponse = $reserveResponse;
        $this->cancellationResponse = $cancellationResponse;
    }

    /**
     * @return HotelBedsOrderInput|HotelOrderInput
     */
    public function orderInput()
    {
        return $this->input;
    }

    /**
     * @return string
     */
    public function description()
    {
        return 'Hotel order ID = ' . $this->idValue();
    }

    public function idValue()
    {
       return $this->id->id();
    }


    /**
     * @return int
     */
    public function type()
    {
        return OrderType::HOTEL;
    }

    /**
     * @return HotelOfferItem
     */
    public function hotelOffer(): HotelOfferItem
    {
        return $this->hotelOffer;
    }

    /**
     * @return HotelInfo
     */
    public function hotelInfo(): HotelInfo
    {
        return $this->hotelInfo;
    }

    /**
     * @return null
     */
    public function reserveResponse()
    {
        return $this->reserveResponse;
    }

    /**
     * @return CarCancelReservationResponse|null
     */
    public function cancellationResponse(): ?CarCancelReservationResponse
    {
        return $this->cancellationResponse;
    }

    /**
     * @param $userContext
     */
    public function process($userContext)
    {
        /** @var RedemptionConfigManager $redemptionConfigManager */
        /** @var HpHotelService $hotelService */
        $redemptionConfigManager = app(RedemptionConfigManager::class);
        $hotelService = app(HpHotelService::class);

        $invoice = $this->invoice();
        $userContext = $this->context();
        $hotelOffer = $this->hotelOffer();
        $orderInput = $this->orderInput();

        $hotelService->validateVisitors($hotelOffer->code, $orderInput);

        if ($redemptionConfigManager->config($userContext, TransClass::HOTEL)->allowLiveOrder) {
            $hotelReservationResponse = $hotelService->bookOffer($hotelOffer->code, $orderInput);
            $this->reserveResponse = $hotelReservationResponse;

            $this->logVerificationError($hotelReservationResponse, $invoice, $userContext->networkId);
        }
        $this->approve();
    }

    /**
     * @param $userContext
     */
    public function cancel($userContext)
    {
        // todo implement car reservation cancel
    }

    /**
     * @param HotelApiServiceReserveResponse $hotelReservationResponse
     * @param InvoiceAmount $invoice
     * @param $networkId
     */
    private function logVerificationError(HotelApiServiceReserveResponse $hotelReservationResponse, InvoiceAmount $invoice, $networkId)
    {
        $hotelPriceCalculator = app(HotelPriceCalculator::class);

        if ($invoice->priceInPoints != $hotelPriceCalculator->points($hotelReservationResponse->originalPrice, $hotelReservationResponse->originalCurrency, $networkId)) {
            Log::error('Hotel Reservation: amount returned from Hotel Reservation Service does\'t match to invoiced amount. API Service Response: ' . json_encode($hotelReservationResponse) . '. Invoiced: ' . json_encode($invoice));
        }
    }

    /**
     * @return Points
     */
    public function payInPoints(): Points
    {
        // TODO: Implement payInPoints() method.
    }
}

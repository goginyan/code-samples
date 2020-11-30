<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\Config\RedemptionConfigManager;
use App\Components\Redemption\Hotel\HotelPro\HpHotelService;
use App\Components\Redemption\HotelBeds\RedemptionTransaction\HotelBedsRedemptionService;
use App\Components\Redemption\Invoice\InvoiceAmount;
use App\Exceptions\RedemptionException;
use App\Repositories\RepositoryUser;
use App\Services\Auth\UserContext;
use App\Services\TransClass;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HotelBedsReservationServiceLegacy
{
    /**
     * @var \App\Repositories\RepositoryUser
     */
    private $userRepository;
    /**
     * @var \App\Components\Redemption\Hotel\RedemptionTransaction\HotelRedemptionService
     */
    private $redemptionService;
    /**
     * @var HpHotelService
     */
    private $hotelService;
    /**
     * @var HotelBedsPriceCalculator
     */
    private $priceCalculator;
    /**
     * @var RedemptionConfigManager
     */
    private $redemptionConfigManager;

    /**
     * @var HotelBedsExceptionHandler
     */
    private $hotelBedsExceptions;

    /**
     * HotelReservationService constructor.
     *
     * @param RepositoryUser             $userRepository
     * @param HotelBedsAdapter           $hotelService
     * @param HotelBedsRedemptionService $redemptionService
     * @param HotelBedsPriceCalculator   $priceCalculator
     * @param RedemptionConfigManager    $redemptionConfigManager
     */
    public function __construct(RepositoryUser $userRepository,
                                HotelBedsAdapter $hotelService,
                                HotelBedsRedemptionService $redemptionService,
                                HotelBedsPriceCalculator $priceCalculator,
                                RedemptionConfigManager $redemptionConfigManager)
    {
        $this->userRepository = $userRepository;
        $this->redemptionService = $redemptionService;
        $this->hotelService = $hotelService;
        $this->priceCalculator = $priceCalculator;
        $this->redemptionConfigManager = $redemptionConfigManager;
        $this->hotelBedsExceptions = new HotelBedsExceptionHandler();
    }

    /**
     * @param UserContext $context
     * @param InvoiceAmount $invoice
     * @param HotelBedsOrderInput $orderInput
     * @return mixed|object|null
     * @throws RedemptionException
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function reserve(UserContext $context, InvoiceAmount $invoice, HotelBedsOrderInput $orderInput)
    {
        $balance = $this->userRepository->lockForUpdate()->balanceOfNetwork($context->userId, $context->networkId);

        if ($balance >= $invoice->payInPoints) {

            if($this->redemptionConfigManager->config($context, TransClass::HOTEL)->allowLiveOrder) {

                $hotelReservationResponse = $this->hotelService->bookings($orderInput, $context->networkId);

                $this->logVerificationError($hotelReservationResponse, $invoice, $context->networkId);
            }

            if (!$transactionId = $this->redemptionService->redeem($context, $invoice, $orderInput, $hotelReservationResponse ?? null)){
                throw new RuntimeException('Redemption function failed, see Laravel logs');
            }

            return $transactionId;
        }

        throw RedemptionException::notEnoughBalance();
    }

    /**
     * @param HotelBedsOrderItem $hotelReservationResponse
     * @param InvoiceAmount $invoice
     * @param $networkId
     */
    private function logVerificationError(HotelBedsOrderItem $hotelReservationResponse, InvoiceAmount $invoice, $networkId)
    {
        if ($invoice->priceInPoints != $hotelReservationResponse->offerInfo->totalNet->points) {
            Log::error('Hotel Beds Reservation: amount returned from Hotel Reservation Service does\'t match to invoiced amount. API Service Response: ' . json_encode($hotelReservationResponse) . '. Invoiced: ' . json_encode($invoice));
        }
    }
}

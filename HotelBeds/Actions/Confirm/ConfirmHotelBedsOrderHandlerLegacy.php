<?php

namespace App\Components\Redemption\HotelBeds\Actions\Confirm;

use App\Components\Balance\Repositories\Account\UserAccountRepository;
use App\Components\Redemption\Hotel\HotelPriceCalculator;
use App\Components\Redemption\Hotel\HotelPro\HpHotelService;
use App\Components\Redemption\Hotel\HotelRedemptionTypeValidator;
use App\Components\Redemption\Hotel\HotelReservationService;
use App\Components\Redemption\Hotel\PendingTransaction\HotelPendingTransactionService;
use App\Components\Redemption\HotelBeds\HotelBedsReservationServiceLegacy;
use App\Components\Redemption\HotelBeds\HotelBedsAdapter;
use App\Components\Redemption\HotelBeds\PendingTransaction\HotelBedsPendingTransactionService;
use App\Components\Redemption\HotelBeds\RedemptionTransaction\HotelBedsRedemptionTransaction;
use App\Components\Redemption\Invoice\InvoiceAmountService;
use App\Components\Redemption\Order\AmountVerifier;
use App\Components\Redemption\RedemptionMessageBag;
use App\Exceptions\RedemptionException;
use App\Services\Partner\PartnerServiceResolver;
use App\Services\TransClass;


class ConfirmHotelBedsOrderHandlerLegacy
{
    /**
     * @var HpHotelService
     */
    private $hotelService;
    /**
     * @var InvoiceAmountService
     */
    private $invoiceAmountService;
    /**
     * @var HotelPendingTransactionService
     */
    private $pendingTransactionService;

    /** @var PartnerServiceResolver */
    private $partnerServiceResolver;
    /**
     * @var HotelReservationService
     */
    private $hotelReservationService;
    /**
     * @var \App\Components\Redemption\Order\AmountVerifier
     */
    private $amountVerifier;
    /**
     * @var \App\Components\Balance\Repositories\Account\UserAccountRepository
     */
    private $balanceRepository;
    private $priceCalculator;

    /**
     * ConfirmHotelOrderHandler constructor.
     *
     * @param HotelBedsAdapter                                                                           $hotelService
     * @param InvoiceAmountService                                                                       $invoiceAmountService
     * @param \App\Components\Redemption\HotelBeds\PendingTransaction\HotelBedsPendingTransactionService $pendingTransactionService
     * @param PartnerServiceResolver                                                                     $partnerServiceResolver
     * @param \App\Components\Redemption\HotelBeds\HotelBedsReservationServiceLegacy                     $itemsOrderService
     * @param \App\Components\Redemption\Order\AmountVerifier                                            $amountVerifier
     * @param \App\Components\Balance\Repositories\Account\UserAccountRepository                         $balanceRepository
     * @param \App\Components\Redemption\Hotel\HotelPriceCalculator                                      $priceCalculator
     */
    public function __construct(
        HotelBedsAdapter $hotelService,
        InvoiceAmountService $invoiceAmountService,
        HotelBedsPendingTransactionService $pendingTransactionService,
        PartnerServiceResolver $partnerServiceResolver,
        HotelBedsReservationServiceLegacy $itemsOrderService,
        AmountVerifier $amountVerifier,
        UserAccountRepository $balanceRepository,
        HotelPriceCalculator $priceCalculator
    )
    {

        $this->priceCalculator = $priceCalculator;
        $this->hotelService = $hotelService;
        $this->invoiceAmountService = $invoiceAmountService;
        $this->pendingTransactionService = $pendingTransactionService;
        $this->partnerServiceResolver = $partnerServiceResolver;
        $this->hotelReservationService = $itemsOrderService;
        $this->amountVerifier = $amountVerifier;
        $this->balanceRepository = $balanceRepository;
    }

    /**
     * @param \App\Components\Redemption\HotelBeds\Actions\Confirm\ConfirmHotelBedsOrderRequestLegacy $request
     *
     * @return \App\Components\Redemption\HotelBeds\Actions\Confirm\ConfirmHotelBedsOrderResponseLegacy
     * @throws \App\Exceptions\HotelBeadsException
     * @throws \App\Exceptions\RedemptionException
     */
    public function handle(ConfirmHotelBedsOrderRequestLegacy $request)
    {
        $hotelInfo = $this->hotelService->checkBookingRates($request->orderInput->rateKey, $request->context->networkId);

        $priceInPoints = $hotelInfo->priceInPoints();

        $balance = $this->balanceRepository->userAccount($request->context->userId, $request->context->networkId)->points;

        $invoice = $this->invoiceAmountService->invoiceFromPoints($request->context, $priceInPoints, $balance, TransClass::HOTEL);

        (new HotelRedemptionTypeValidator($request->context, $invoice, $balance))
            ->validate($errorHandler = new RedemptionMessageBag());

        if ($error = $errorHandler->firstFailedMessage()) {
            throw RedemptionException::balanceValidationFailed($error->message, $error->code);
        }
        $this->amountVerifier->verify($invoice, $request->pointsToPay, $request->payInCash);

        $transactionId = $this->hotelReservationService->reserve($request->context, $invoice, $request->orderInput);

        return new ConfirmHotelBedsOrderResponseLegacy(
            $invoice,
            $redirectPage ?? '',
            isset($transactionId) ? HotelBedsRedemptionTransaction::createFromTransactionId($transactionId) : null
        );
    }
}
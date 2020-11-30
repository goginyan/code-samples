<?php

namespace App\Components\Redemption\HotelBeds\Actions\Confirm;

use App\Components\Redemption\Invoice\InvoiceAmount;

class ConfirmHotelBedsOrderResponseLegacy
{
    /**
     * @var \App\Components\Redemption\Invoice\InvoiceAmount
     */
    public $invoice;
    public $redirectPage;
    public $transaction;

    /**
     * ConfirmHotelBedsOrderResponseLegacy constructor.
     * @param InvoiceAmount $invoice
     * @param $redirectPage
     * @param $transaction
     */
    public function __construct(InvoiceAmount $invoice, $redirectPage, $transaction)
    {
        $this->invoice = $invoice;
        $this->redirectPage = $redirectPage;
        $this->transaction = $transaction;
    }
}
<?php

namespace App\Components\Redemption\HotelBeds;

use App\Infrastructure\Versioned\Versioned;

class HotelBedsOfferItem extends  Versioned
{
    protected static $version = 1;

    public $reference;

    public $clientReference;

    public $creationDate;

    public $status;

    public $modificationPolicies;

    public $remark;

    public $invoiceCompany;

    public $totalNet;

    public $currency;

    public $rates;

    /**
     * HotelBedsOfferItem constructor.
     * @param $reference
     * @param $clientReference
     * @param $creationDate
     * @param $status
     * @param $modificationPolicies
     * @param $remark
     * @param $invoiceCompany
     * @param $totalNet
     * @param $currency
     * @param $rates
     */
    public function __construct($reference,
                                $clientReference,
                                $creationDate,
                                $status,
                                $modificationPolicies,
                                $remark,
                                $invoiceCompany,
                                $totalNet,
                                $currency,
                                $rates
                                )
    {
        $this->reference = $reference;
        $this->clientReference = $clientReference;
        $this->creationDate = $creationDate;
        $this->status = $status;
        $this->modificationPolicies = $modificationPolicies;
        $this->remark = $remark;
        $this->invoiceCompany = $invoiceCompany;
        $this->totalNet = $totalNet;
        $this->currency = $currency;
        $this->rates = $rates;
    }

    /**
     * @param $data
     * @return HotelBedsOfferItem
     */
    public static function fromArray($data)
    {
        return new self(
            $data['reference'],
            $data['client_reference'],
            $data['creation_date'],
            $data['status'],
            $data['modification_policies'],
            $data['remark'],
            $data['invoice_company'],
            $data['total_net'],
            $data['currency'],
            $data['rates']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'reference' => $this->reference,
            'client_reference' => $this->clientReference,
            'creation_date' => $this->creationDate,
            'status' => $this->status,
            'modification_policies' => $this->modificationPolicies,
            'remark' => $this->remark,
            'invoice_company' => $this->invoiceCompany,
            'total_net' => $this->totalNet,
            'currency' => $this->currency,
            'rates' => $this->rates,
        ];
    }
}

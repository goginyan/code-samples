<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\Order\OrderInput;
use App\Infrastructure\ConfigurationManager\ComponentConfigManager;

class HotelBedsPaymentCardInput
{
    public $cardHolderName;
    public $cardType;
    public $cardNumber;
    public $expiryDate;
    public $cardCvc;

    /**
     * HotelBedsPaymentCardInput constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->cardHolderName = $config['card_holder_name'] ?? null;
        $this->cardType = $config['card_type'] ?? null;
        $this->cardNumber = $config['card_number']?? null;
        $this->expiryDate = $config['expiry_date']?? null;
        $this->cardCvc = $config['card_cvc']?? null;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'paymentCard' =>[
                'cardHolderName'=>$this->cardHolderName,
                'cardType'=>$this->cardType,
                'cardNumber'=>$this->cardNumber,
                'expiryDate'=>$this->expiryDate,
                'cardCvc'=>$this->cardCvc
            ]
    ];
    }

}

<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\NotifChannelMessageData;

use Data\DataTransferObjects\PriceDTO;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use DateTime;
use Notifications\Enums\NotificationChannelEnum;
use Spatie\DataTransferObject\DataTransferObject;

class KlaviyoMessageDataDTO extends DataTransferObject implements IMessageDataDTO
{
    use Translatable;

    public function getChannel(): NotificationChannelEnum
    {
        return NotificationChannelEnum::KLAVIYO();
    }

    public ?string $customerEmail;
    public ?string $customerFirstname;
    public ?string $customerLastname;
    public ?string $customerID;
    public ?string $customerPhone;

    public ?bool $customersIsAcceptsMarketing;
    public ?int $customersOrdersCount;
    public ?PriceDTO $customersTotalSpent;

    public ?string $customerCity;
    public ?string $customerZip;
    public ?string $customerCountry;

    public ?string $trackingNumber;
    public ?string $trackingLink;

    public ?string $shipmentImageURL;
    public ?string $shipmentTitle;
    public ?string $shipmentStatusSlug;
    public ?string $shipmentSubStatusSlug;
    public ?TranslationDTO $shipmentStatus;
    public ?TranslationDTO $shipmentSubStatus;

    public ?string $courierTrackingNumber;
    public ?string $courierTrackingLink;
    public ?string $courierName;
    public ?string $courierLogo;
    public ?string $courierLogoSVG;
    public ?string $courierWebsite;
    public ?string $courierPhone;

    public ?string $lastCheckpointDescription;
    public ?DateTime $lastCheckpointTimestamp;
    public ?string $lastCheckpointCounty;
    public ?string $lastCheckpointState;
    public ?string $lastCheckpointZIP;
    public ?string $lastCheckpointCity;
    public ?float $lastCheckpointLatitude;
    public ?float $lastCheckpointLongitude;

    public ?string $shippingAddressAddress;
    public ?string $shippingAddressCity;
    public ?string $shippingAddressCountry;
    public ?string $shippingAddressZIP;

    public ?int $orderID;
    public ?int $shopifyOrderId;
    public ?string $orderCurrency;
    public ?PriceDTO $orderPrice;
    public ?string $orderAdminLink;

    public function toArray(): array
    {
        return [
            'customerEmail' => $this->customerEmail,
            'customerFirstname' => $this->customerFirstname,
            'customerLastname' => $this->customerLastname,
            'customerID' => $this->customerID,
            'customerPhone' => $this->customerPhone,

            'customersIsAcceptsMarketing' => $this->customersIsAcceptsMarketing,
            'customersOrdersCount' => $this->customersOrdersCount,
            'customersTotalSpent' => $this->customersTotalSpent ? $this->customersTotalSpent->getNormalPrice() : null,

            'customerCity' => $this->customerCity,
            'customerZip' => $this->customerZip,
            'customerCountry' => $this->customerCountry,

            'trackingNumber' => $this->trackingNumber,
            'trackingLink' => $this->trackingLink,

            'shipmentImageURL' => $this->shipmentImageURL,
            'shipmentTitle' => $this->shipmentTitle,
            'shipmentStatusSlug' => $this->shipmentStatusSlug,
            'shipmentSubStatusSlug' => $this->shipmentSubStatusSlug,
            'shipmentStatus' => $this->shipmentStatus->getText(),
            'shipmentSubStatus' => $this->shipmentSubStatus->getText(),

            'courierTrackingNumber' => $this->courierTrackingNumber,
            'courierTrackingLink' => $this->courierTrackingLink,
            'courierName' => $this->courierName,
            'courierLogo' => $this->courierLogo,
            'courierLogoSVG' => $this->courierLogoSVG,
            'courierWebsite' => $this->courierWebsite,
            'courierPhone' => $this->courierPhone,

            'lastCheckpointDescription' => $this->lastCheckpointDescription,
            'lastCheckpointTimestamp' => $this->lastCheckpointTimestamp,
            'lastCheckpointCounty' => $this->lastCheckpointCounty,
            'lastCheckpointState' => $this->lastCheckpointState,
            'lastCheckpointZIP' => $this->lastCheckpointZIP,
            'lastCheckpointCity' => $this->lastCheckpointCity,
            'lastCheckpointLatitude' => $this->lastCheckpointLatitude,
            'lastCheckpointLongitude' => $this->lastCheckpointLongitude,

            'shippingAddressAddress' => $this->shippingAddressAddress,
            'shippingAddressCity' => $this->shippingAddressCity,
            'shippingAddressCountry' => $this->shippingAddressCountry,
            'shippingAddressZIP' => $this->shippingAddressZIP,

            'orderID' => $this->orderID,
            'shopifyOrderId' => $this->shopifyOrderId,
            'orderCurrency' => $this->orderCurrency,
            'orderPrice' => $this->orderPrice,
            'orderAdminLink' => $this->orderAdminLink,
        ];
    }
}

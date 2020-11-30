<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\NotifChannelMessageData;

use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Notifications\Enums\NotificationChannelEnum;
use Spatie\DataTransferObject\DataTransferObject;

class SlackMessageDataDTO extends DataTransferObject implements IMessageDataDTO
{
    use Translatable;

    public function getChannel(): NotificationChannelEnum
    {
        return NotificationChannelEnum::SLACK();
    }

    public ?TranslationDTO $trigger;

    public ?string $shipmentImageURL;
    public ?string $lastCheckpointDescription;

    public ?string $shippingAddressAddress;
    public ?string $shippingAddressCity;
    public ?string $shippingAddressCountry;
    public ?string $shippingAddressZIP;

    public ?string $trackingNumber;
    public ?string $trackingLink;

    public ?string $shipmentTitle;
    public ?string $shipmentStatusSlug;
    public ?string $shipmentSubStatusSlug;
    public ?TranslationDTO $shipmentStatus;
    public ?TranslationDTO $shipmentSubStatus;

    public ?string $courierTrackingNumber;
    public ?string $courierTrackingLink;
    public ?string $courierName;
    public ?string $courierLogoNonSVG;
    public ?string $courierWebsite;
    public ?string $courierPhone;

    public ?int $orderID;
    public ?string $orderAdminLink;

    /*
    public ?string $customerEmail;
    public ?string $customerFirstname;
    public ?string $customerLastname;
    public ?string $customerID;
    public ?string $customerPhone;
    public ?string $customerCity;
    public ?string $customerCountry;

    public ?DateTime $lastCheckpointTimestamp;
    public ?string $lastCheckpointCounty;
    public ?string $lastCheckpointState;
    public ?string $lastCheckpointZIP;
    public ?string $lastCheckpointCity;
    public ?float $lastCheckpointLatitude;
    public ?float $lastCheckpointLongitude;
    public ?string $orderPrice;
    */

    public function toArray(): array
    {
        return array_filter([
            /*
            'customer_email' => $this->customerEmail,
            'customer_firstname' => $this->customerFirstname,
            'customer_lastname' => $this->customerLastname,
            'customer_id' => $this->customerID,
            'customer_phone' => $this->customerPhone,
            'customer_city' => $this->customerCity,
            'customer_country' => $this->customerCountry,

            'last_checkpoint_timestamp' => $this->lastCheckpointTimestamp,
            'last_checkpoint_county' => $this->lastCheckpointCounty,
            'last_checkpoint_state' => $this->lastCheckpointState,
            'last_checkpoint_zip' => $this->lastCheckpointZIP,
            'last_checkpoint_city' => $this->lastCheckpointCity,
            'last_checkpoint_latitude' => $this->lastCheckpointLatitude,
            'last_checkpoint_longitude' => $this->lastCheckpointLongitude,

            'order_price' => $this->orderPrice,
            */

            'tracking_number' => $this->trackingNumber,
            'tracking_link' => $this->trackingLink,

            'shipment_image_url' => $this->shipmentImageURL,
            'shipment_title' => $this->shipmentTitle,
            'shipment_status' => $this->shipmentStatus ? $this->shipmentStatus->getText() : null,
            'shipment_substatus' => $this->shipmentSubStatus ? $this->shipmentSubStatus->getText() : null,

            'courier_tracking_number' => $this->courierTrackingNumber,
            'courier_tracking_link' => $this->courierTrackingLink,
            'courier_name' => $this->courierName,
            'courier_logo_non_svg' => $this->courierLogoNonSVG,
            'courier_website' => $this->courierWebsite,
            'courier_phone' => $this->courierPhone,

            'last_checkpoint_description' => $this->lastCheckpointDescription,

            'shipping_address_address' => $this->shippingAddressAddress,
            'shipping_address_city' => $this->shippingAddressCity,
            'shipping_address_country' => $this->shippingAddressCountry,
            'shipping_address_zip' => $this->shippingAddressZIP,

            'order_id' => $this->orderID,
            'order_admin_link' => $this->orderAdminLink,
        ]);
    }
}

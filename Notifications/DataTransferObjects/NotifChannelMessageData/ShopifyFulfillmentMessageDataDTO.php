<?php

namespace Notifications\DataTransferObjects\NotifChannelMessageData;

use DateTime;
use Notifications\Enums\NotificationChannelEnum;
use Shipments\Enums\ShipmentStatusEnum;
use Shipments\Enums\ShipmentSubStatusEnum;
use Shipments\Enums\ShopifyShipmentStatusEnum;
use Shipments\Mappers\RushShopifyShipmentStatusMapper;

class ShopifyFulfillmentMessageDataDTO implements IMessageDataDTO
{
    public int $shopifyStoreID;
    public string $myShopifyDomain;
    public string $accessToken;
    public int $shopifyOrderID;
    public int $shopifyFulfillmentID;
    public ShipmentStatusEnum $status;
    public ?ShipmentSubStatusEnum $substatus;

    public ?ShopifyShipmentStatusEnum $shopifyShipmentStatus;

    public ?DateTime  $estimatedDeliveryAt;

    public ?string  $message;
    public ?DateTime $happenedAt;
    public ?string  $city;
    public ?string  $province;
    public ?string  $country;
    public ?string  $zip;
    public ?string  $address1;
    public ?float  $latitude;
    public ?float  $longitude;

    public function __construct(
        int $shopifyStoreID,
        string $myShopifyDomain,
        string $accessToken,
        int $shopifyOrderID,
        int $shopifyFulfillmentID,
        ShipmentStatusEnum $status,
        ?ShipmentSubStatusEnum $substatus,
        ?DateTime $estimatedDeliveryAt
    ) {
        $this->shopifyStoreID = $shopifyStoreID;
        $this->myShopifyDomain = $myShopifyDomain;
        $this->accessToken = $accessToken;

        $this->shopifyOrderID = $shopifyOrderID;
        $this->shopifyFulfillmentID = $shopifyFulfillmentID;
        $this->status = $status;
        $this->substatus = $substatus;
        $this->estimatedDeliveryAt = $estimatedDeliveryAt;

        $this->message = null;
        $this->happenedAt = null;
        $this->city = null;
        $this->province = null;
        $this->country = null;
        $this->zip = null;
        $this->address1 = null;
        $this->latitude = null;
        $this->longitude = null;

        $this->shopifyShipmentStatus = RushShopifyShipmentStatusMapper::map($this->status, $this->substatus);
    }

    public function getChannel(): NotificationChannelEnum
    {
        return NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT();
    }

    public function toArray(): array
    {
        return array_filter([
            'fulfillment_id' => $this->shopifyFulfillmentID,
            'status' => $this->status->value,
            'substatus' => $this->substatus->value,
            'message' => $this->message,

            'happenedAt' => $this->happenedAt ? $this->happenedAt->format('Y-m-d H:i:s') : null,

            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'zip' => $this->zip,
            'address1' => $this->address1,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'shopify_store_id' => $this->shopifyStoreID,
            'estimated_delivery_at' => $this->estimatedDeliveryAt ? $this->estimatedDeliveryAt->format('Y-m-d H:i:s') : null,
            'shopify_order_id' => $this->shopifyOrderID,
        ]);
    }

    public function isValidShopifyFulfilmentEventStatus(): bool
    {
        return ! is_null($this->shopifyShipmentStatus);
    }

    public function toShopifyArray(): array
    {
        return [
            'event' => array_filter([
                'fulfillmentId' => $this->shopifyFulfillmentID,
                'status' =>$this->shopifyShipmentStatus->value,
                'message' => $this->message,

                'happenedAt' => $this->happenedAt ? $this->happenedAt->format(\DateTimeInterface::ISO8601
    ) : null,

                'city' => $this->city,
                'province' => $this->province,
                'country' => $this->country,
                'zip' => $this->zip,
                'address1' => $this->address1,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'shopId' => $this->shopifyStoreID,
                'estimatedDeliveryAt' => $this->estimatedDeliveryAt ? $this->estimatedDeliveryAt->format(\DateTimeInterface::ISO8601
    ) : null,
                'order_id' => $this->shopifyOrderID,
            ]),
        ];
    }
}

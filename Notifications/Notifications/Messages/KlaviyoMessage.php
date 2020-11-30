<?php

declare(strict_types=1);

namespace Notifications\Notifications\Messages;

use Data\Enums\LanguageEnum;
use DateTime;
use Notifications\DataTransferObjects\NotifChannelMessageData\KlaviyoMessageDataDTO;
use Notifications\Enums\NotificationTriggerEnum;
use Store\Services\GAUTMBuilderService;

class KlaviyoMessage
{
    /**
     * Read more about Klaviyo format of messaging here:
     * - https://www.klaviyo.com/docs/http-api#events
     * - https://help.klaviyo.com/hc/en-us/articles/115000751052-Klaviyo-API-Reference-Guide#tracking-events--server-side-10
     * - https://github.com/klaviyo/php-klaviyo
     * Last is nice library for it, but seems it is overkill for simple API request.
     * @param string $public_api_key
     * @param string $event_id
     * @param KlaviyoMessageDataDTO $dto
     * @param NotificationTriggerEnum $triggerEnum
     * @return array
     */
    public static function EVENT(string $public_api_key, NotificationTriggerEnum $trigger, KlaviyoMessageDataDTO $dto, LanguageEnum $language, NotificationTriggerEnum $triggerEnum, ?string $eventID): array
    {
        $notificationSlug = 'RUSH_'.strtoupper(NotificationTriggerEnum::toSlug($trigger));

        $now = new DateTime();
        $dto->translate(LanguageEnum::toSlug($language));
        $out = [
            'token' => $public_api_key,
            'event' => $notificationSlug,
            'customer_properties' => self::_EVENT_CREATE_CUSTOMER_PROPERTIES($dto),
            'properties' => [
                'tracking' => self::_EVENT_CREATE_TRACKING_PROPERTIES($dto, $triggerEnum),
                'last_checkpoint' => self::_EVENT_CREATE_LAST_CHECKPOINT_PROPERTIES($dto),
                'shipping_address' => self::_EVENT_SHIPPING_ADDRESS_TRACKING_PROPERTIES($dto),
                'order' => self::_EVENT_CREATE_ORDER_PROPERTIES($dto, $triggerEnum),
                'carrier' => self::_EVENT_CREATE_CARRIER_PROPERTIES($dto, $triggerEnum),
            ],
            'time' => $now->getTimestamp(),
        ];
        if (! is_null($eventID)) {
            $out['properties']['$event_id'] = $eventID;
        }

        return $out;
    }

    private static function _EVENT_CREATE_CUSTOMER_PROPERTIES(KlaviyoMessageDataDTO $dto): array
    {
        $out = self::_LOOPLIST([
            'customerEmail' => '$email',
            'customerFirstname' => '$first_name',
            'customerLastname' => '$last_name',
            'customerPhone' => '$phone_number',
            'customerCity' => '$city',
            'customerZip' => '$zip',
            'customerCountry' => '$country',
            'customersIsAcceptsMarketing' => '$consent',
            'customerID' => 'shopify_customer_id',
            'customersOrdersCount' => 'orders_count',
        ], $dto);
        if (! is_null($dto->customersTotalSpent)) {
            $out['orders_total_value'] = $dto->customersTotalSpent->getNormalPrice();
        }

        return $out;
    }

    private static function _EVENT_CREATE_TRACKING_PROPERTIES(KlaviyoMessageDataDTO $dto, NotificationTriggerEnum $triggerEnum): array
    {
        $tracking = self::_LOOPLIST([
            'trackingNumber' => 'tracking_number',
            'trackingLink' => 'tracking_link',
            'shipmentImageURL' => 'shipment_image_link',
            'shipmentTitle' => 'shipment_title',
        ], $dto);

        $tracking['shipment_status'] = $dto->shipmentStatusSlug;
        $tracking['shipment_status_label'] = is_null($dto->shipmentStatus) ? null : $dto->shipmentStatus->getText();

        $tracking['shipment_substatus'] = $dto->shipmentSubStatusSlug;
        $tracking['shipment_substatus_label'] = is_null($dto->shipmentSubStatus) ? null : $dto->shipmentSubStatus->getText();

        self::_applyGAUTMBuilderService($tracking, 'tracking_link', $dto, $triggerEnum);

        return $tracking;
    }

    private static function _EVENT_CREATE_LAST_CHECKPOINT_PROPERTIES(KlaviyoMessageDataDTO $dto): ?array
    {
        $lastCheckpoint = self::_LOOPLIST([
            'lastCheckpointDescription' => 'description',
            'lastCheckpointCounty' => 'county',
            'lastCheckpointState' => 'state',
            'lastCheckpointZIP' => 'zip',
            'lastCheckpointCity' => 'city',
            'lastCheckpointLatitude' => 'latitude',
            'lastCheckpointLongitude' => 'longitude',
        ], $dto);
        if (count($lastCheckpoint) == 0) {
            return null;
        }

        if (! is_null($dto->lastCheckpointTimestamp)) {
            $lastCheckpoint['timestamp'] = $dto->lastCheckpointTimestamp->format('Y-m-d H:i:s');
        }

        return $lastCheckpoint;
    }

    private static function _EVENT_SHIPPING_ADDRESS_TRACKING_PROPERTIES(KlaviyoMessageDataDTO $dto): array
    {
        return self::_LOOPLIST([
            'shippingAddressAddress' => 'address',
            'shippingAddressCity' => 'city',
            'shippingAddressCountry' => 'country',
            'shippingAddressZIP' => 'zip',
        ], $dto);
    }

    private static function _EVENT_CREATE_ORDER_PROPERTIES(KlaviyoMessageDataDTO $dto, NotificationTriggerEnum $triggerEnum): array
    {
        $order = self::_LOOPLIST([
            'orderID' => 'id',
            'orderCurrency' => 'currency',
            'shopifyOrderId' => 'shopify_order_id',
            'orderAdminLink' => 'admin_link',
        ], $dto);

        $order['price'] = $dto->orderPrice->getNormalPrice();

        self::_applyGAUTMBuilderService($order, 'admin_link', $dto, $triggerEnum);

        return $order;
    }

    private static function _EVENT_CREATE_CARRIER_PROPERTIES(KlaviyoMessageDataDTO $dto, NotificationTriggerEnum $triggerEnum): array
    {
        $carrier = self::_LOOPLIST([
            'courierTrackingNumber' => 'tracking_number',
            'courierTrackingLink' => 'tracking_link',
            'courierName' => 'name',
            'courierLogo' => 'logo_link',
            'courierLogoSVG' => 'logo_svg_link',
            'courierWebsite' => 'website',
            'courierPhone' => 'phone',
        ], $dto);

        self::_applyGAUTMBuilderService($carrier, 'tracking_link', $dto, $triggerEnum);
        self::_applyGAUTMBuilderService($carrier, 'website', $dto, $triggerEnum);

        return $carrier;
    }

    /**
     * Apply google analytics UTM tags.
     * @param array $arr
     * @param string $field
     * @param KlaviyoMessageDataDTO $dto
     * @param NotificationTriggerEnum $triggerEnum
     */
    private static function _applyGAUTMBuilderService(array &$arr, string $field, KlaviyoMessageDataDTO $dto, NotificationTriggerEnum $triggerEnum): void
    {
        if (isset($arr[$field])) {
            $arr[$field] = GAUTMBuilderService::notificationChannel(
                $arr[$field],
                $dto->shipmentStatusSlug,
                $triggerEnum,
                $dto->getChannel()
            );
        }
    }

    private static function _LOOPLIST(array $mapping_list, object $source): array
    {
        $out = [];

        foreach ($mapping_list as $key => $value) {
            if (! is_null($source->{$key})) {
                $out[$value] = $source->{$key};
            }
        }

        return $out;
    }
}

<?php

namespace Notifications\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self NEW_CHECKPOINT()
 * @method static self NEW_SHIPMENT()
 * @method static self SHIPMENT_NEW_STATUS()
 * @method static self SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION()
 * @method static self FORECASTED_POSSIBLE_DELAY()
 * @method static self PACKAGE_IN_DESTINATION_COUNTRY()
 * @method static self PACKAGE_LEFT_ORIGIN_COUNTRY()
 */
final class NotificationTriggerEnum extends Enum
{
    protected static function values(): array
    {
        return ['NEW_CHECKPOINT' => 1, 'NEW_SHIPMENT' => 2, 'SHIPMENT_NEW_STATUS' => 3, 'SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION' => 4, 'FORECASTED_POSSIBLE_DELAY' => 5, 'PACKAGE_IN_DESTINATION_COUNTRY' => 6, 'PACKAGE_LEFT_ORIGIN_COUNTRY' => 7];
    }

    /**
     * Check if value exist based on availbale.
     * @param int $value
     * @return bool
     */
    public static function exist(string $value): bool
    {
        return in_array($value, self::values());
    }

    public static function fromSlug(string $slug): ?self
    {
        if ($slug == 'new_checkpoint') {
            return self::NEW_CHECKPOINT();
        } elseif ($slug == 'new_shipment') {
            return self::NEW_SHIPMENT();
        } elseif ($slug == 'shipment_new_status') {
            return self::SHIPMENT_NEW_STATUS();
        } elseif ($slug == 'shipment_same_checkpoint_for_x') {
            return self::SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION();
        } elseif ($slug == 'forecasted_possible_delay') {
            return self::FORECASTED_POSSIBLE_DELAY();
        } elseif ($slug == 'package_in_destination_country') {
            return self::PACKAGE_IN_DESTINATION_COUNTRY();
        } elseif ($slug == 'package_left_origin_country') {
            return self::PACKAGE_LEFT_ORIGIN_COUNTRY();
        }

        return null;
    }

    public static function toSlug(self $enum): ?string
    {
        if ($enum->equals(self::NEW_CHECKPOINT())) {
            return 'new_checkpoint';
        } elseif ($enum->equals(self::NEW_SHIPMENT())) {
            return 'new_shipment';
        } elseif ($enum->equals(self::SHIPMENT_NEW_STATUS())) {
            return 'shipment_new_status';
        } elseif ($enum->equals(self::SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION())) {
            return 'shipment_same_checkpoint_for_x';
        } elseif ($enum->equals(self::FORECASTED_POSSIBLE_DELAY())) {
            return 'forecasted_possible_delay';
        } elseif ($enum->equals(self::PACKAGE_IN_DESTINATION_COUNTRY())) {
            return 'package_in_destination_country';
        } elseif ($enum->equals(self::PACKAGE_LEFT_ORIGIN_COUNTRY())) {
            return 'package_left_origin_country';
        }

        return null;
    }

    public static function isSupportShipmentStatus(self $enum): bool
    {
        if ($enum->equals(self::NEW_CHECKPOINT())) {
            return true;
        } elseif ($enum->equals(self::NEW_SHIPMENT())) {
            return false;
        } elseif ($enum->equals(self::SHIPMENT_NEW_STATUS())) {
            return true;
        } elseif ($enum->equals(self::SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION())) {
            return true;
        } elseif ($enum->equals(self::FORECASTED_POSSIBLE_DELAY())) {
            return true;
        } elseif ($enum->equals(self::PACKAGE_IN_DESTINATION_COUNTRY())) {
            return false;
        } elseif ($enum->equals(self::PACKAGE_LEFT_ORIGIN_COUNTRY())) {
            return false;
        }

        return false;
    }

    public static function isSupportNoChangeLimit(self $enum): bool
    {
        if ($enum->equals(self::NEW_CHECKPOINT())) {
            return false;
        } elseif ($enum->equals(self::NEW_SHIPMENT())) {
            return false;
        } elseif ($enum->equals(self::SHIPMENT_NEW_STATUS())) {
            return false;
        } elseif ($enum->equals(self::SHIPMENT_SAME_CHECKPOINT_FOR_X_DURATION())) {
            return true;
        } elseif ($enum->equals(self::FORECASTED_POSSIBLE_DELAY())) {
            return false;
        } elseif ($enum->equals(self::PACKAGE_IN_DESTINATION_COUNTRY())) {
            return false;
        } elseif ($enum->equals(self::PACKAGE_LEFT_ORIGIN_COUNTRY())) {
            return false;
        }

        return false;
    }
}

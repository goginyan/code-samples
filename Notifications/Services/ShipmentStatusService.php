<?php

declare(strict_types=1);

namespace Notifications\Services;

use Data\DataTransferObjects\Translations\TranslationDTO;
use Notifications\DataTransferObjects\Meta\ShipmentStatusDTO;
use Notifications\DataTransferObjects\Meta\ShipmentSubStatusDTO;
use ShopifyStore\DataTransferObjects\Polaris\PolarisBadgeDTO;
use ShopifyStore\Enums\Polaris\PolarisBadgeProgressEnum;
use ShopifyStore\Enums\Polaris\PolarisBadgeStatusEnum;

class ShipmentStatusService
{
    /**
     * @return array
     */
    private static function statuses(): array
    {
        // TODO add all statuses
        return [
            [
                'id' => 'delivered',
                'label' => 'status.delivered',
                'is_final' => true,
                'substatuses' => [
                    [
                        'id' => 'delivered|at_front_door',
                        'label' => 'status.delivered|at_front_door',
                    ],
                    [
                        'id' => 'delivered|mailbox',
                        'label' => 'status.delivered|mailbox',
                    ],
                    [
                        'id' => 'delivered|parcel_locker',
                        'label' => 'status.delivered|parcel_locker',
                    ],
                    [
                        'id' => 'delivered|po_box',
                        'label' => 'status.delivered|po_box',
                    ],
                    [
                        'id' => 'delivered|agent',
                        'label' => 'status.delivered|agent',
                    ],
                    [
                        'id' => 'delivered|person_at_address',
                        'label' => 'status.delivered|person_at_address',
                    ],
                    [
                        'id' => 'delivered|pickup',
                        'label' => 'status.delivered|pickup',
                    ],
                    [
                        'id' => 'delivered|manually_marked',
                        'label' => 'status.delivered|manually_marked',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::ATTENTION(),
                ],
            ],
            [
                'id' => 'pickup',
                'label' => 'status.pickup',
                'is_final' => true,
                'substatuses' => [],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::SUCCESS(),
                ],
            ],
            [
                'id' => 'in_transit',
                'label' => 'status.in_transit',
                'is_final' => false,
                'substatuses' => [
                    [
                        'id' => 'in_transit|at_customs',
                        'label' => 'status.in_transit|at_customs',
                    ],
                    [
                        'id' => 'in_transit|released_customs',
                        'label' => 'status.in_transit|released_customs',
                    ],
                    [
                        'id' => 'in_transit|in_carrier',
                        'label' => 'status.in_transit|in_carrier',
                    ],
                    [
                        'id' => 'in_transit|arrive_at_destination',
                        'label' => 'status.in_transit|arrive_at_destination',
                    ],
                    [
                        'id' => 'in_transit|forwarded',
                        'label' => 'status.in_transit|forwarded',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::ATTENTION(),
                ],
            ],
            [
                'id' => 'out_for_delivery',
                'label' => 'status.out_for_delivery',
                'is_final' => false,
                'substatuses' => [],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::ATTENTION(),
                ],
            ],
            [
                'id' => 'waiting_for_delivery',
                'label' => 'status.waiting_for_delivery',
                'is_final' => false,
                'substatuses' => [
                    [
                        'id' => 'waiting_for_delivery|schedule_redelivery',
                        'label' => 'status.waiting_for_delivery|schedule_redelivery',
                    ],
                    [
                        'id' => 'waiting_for_delivery|wait_per_receiver_request',
                        'label' => 'status.waiting_for_delivery|wait_per_receiver_request',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::WARNING(),
                ],
            ],
            [
                'id' => 'failed_attempt',
                'label' => 'status.failed_attempt',
                'is_final' => false,
                'substatuses' => [
                    [
                        'id' => 'failed_attempt|no_access',
                        'label' => 'status.failed_attempt|no_access',
                    ],
                    [
                        'id' => 'failed_attempt|not_secure',
                        'label' => 'status.failed_attempt|not_secure',
                    ],
                    [
                        'id' => 'failed_attempt|invalid_address',
                        'label' => 'status.failed_attempt|invalid_address',
                    ],
                    [
                        'id' => 'failed_attempt|animal_interference',
                        'label' => 'status.failed_attempt|animal_interference',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::PARTIALLY_COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::CRITICAL(),
                ],
            ],
            [
                'id' => 'exception',
                'label' => 'status.exception',
                'is_final' => false,
                'substatuses' => [
                    [
                        'id' => 'exception|delayed',
                        'label' => 'status.exception|delayed',
                    ],
                    [
                        'id' => 'exception|carrier_not_found',
                        'label' => 'status.exception|carrier_not_found',
                    ],
                    [
                        'id' => 'exception|no_tracking_code_match',
                        'label' => 'status.exception|no_tracking_code_match',
                    ],
                    [
                        'id' => 'exception|error_on_tracking_carrier_side',
                        'label' => 'status.exception|error_on_tracking_carrier_side',
                    ],
                    [
                        'id' => 'exception|tracking_information_invalid',
                        'label' => 'status.exception|tracking_information_invalid',
                    ],
                    [
                        'id' => 'exception|refused_delivery',
                        'label' => 'status.exception|refused_delivery',
                    ],
                    [
                        'id' => 'exception|package_disposal',
                        'label' => 'status.exception|package_disposal',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::EMPTY(),
                    'status' => PolarisBadgeStatusEnum::CRITICAL(),
                ],
            ],
            [
                'id' => 'expired',
                'label' => 'status.expired',
                'is_final' => true,
                'substatuses' => [
                    [
                        'id' => 'expired|tracking_cancelled',
                        'label' => 'status.expired|tracking_cancelled',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::EMPTY(),
                    'status' => PolarisBadgeStatusEnum::CRITICAL(),
                ],
            ],
            [
                'id' => 'pending',
                'label' => 'status.pending',
                'is_final' => true,
                'substatuses' => [
                    [
                        'id' => 'pending|waiting_for_details',
                        'label' => 'status.pending|waiting_for_details',
                    ],
                    [
                        'id' => 'pending|no_tracking_information',
                        'label' => 'status.pending|no_tracking_information',
                    ],
                    [
                        'id' => 'pending|origin_shipment_preparation',
                        'label' => 'status.pending|origin_shipment_preparation',
                    ],
                    [
                        'id' => 'pending|awaiting_shipment',
                        'label' => 'status.pending|awaiting_shipment',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::INCOMPLETE(),
                    'status' => PolarisBadgeStatusEnum::NEW(),
                ],
            ],
            [
                'id' => 'any',
                'label' => 'status.any',
                'is_final' => true,
                'substatuses' => [
                    [
                        'id' => 'any|any',
                        'label' => 'status.any|any',
                    ],
                ],
                'badge_prop' => [
                    'progress' => PolarisBadgeProgressEnum::COMPLETE(),
                    'status' => PolarisBadgeStatusEnum::ATTENTION(),
                ],
            ],
        ];
    }

    /**
     * @return ShipmentStatusDTO[]
     */
    public static function getStatuses(): array
    {
        $statuses = [];
        foreach (self::statuses() as $status) {
            $subStatuses = [];
            foreach ($status['substatuses'] as $subStatus) {
                $subStatuses[] = new ShipmentSubStatusDTO(
                        $subStatus['id'],
                        new TranslationDTO($subStatus['label'])
                    );
            }
            $statuses[] = new ShipmentStatusDTO(
                $status['id'],
                new TranslationDTO($status['label']),
                $status['is_final'],
                PolarisBadgeDTO::make($status['badge_prop']['status'], $status['badge_prop']['progress']),
                $subStatuses
            );
        }

        return $statuses;
    }
}

<?php

declare(strict_types=1);

namespace Notifications\Services;

use App\Enums\EnvironmentEnum;
use App\Repositories\StoreRepository;
use App\Services\SentryService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Notifications\DataTransferObjects\Models\NotificationLogDTO;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\DataTransferObjects\NotifChannelMessageData\IMessageDataDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigBaseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Notifications\NotifiableBase;
use Notifications\Notifications\ShipmentNotificationBase;
use Notifications\Notifications\ShipmentNotificationBaseInQueue;
use Notifications\Repositories\NotificationConfigurationRepository;
use Shipments\Repositories\ShipmentNotificationRepository;
use Shipments\Repositories\ShipmentRepository;

class NotificationManager
{
    /**
     * Send notification.
     * @param NotificationTriggerEnum $trigger
     * @param int $storeId
     * @param int $shipmentId
     * @param int|null $checkpointId
     * @param bool $force
     * @return NotificationLogDTO[]|null
     */
    public static function sendShipment(NotificationTriggerEnum $trigger, int $storeId, int $shipmentId, ?int $checkpointId, bool $force = false): ?array
    {
        $data = [
            'trigger' => $trigger->value,
            'storeId' => $storeId,
            'shipmentId' => $shipmentId,
            'checkpointId' => $checkpointId,
            'force' => $force,
        ];

        SentryService::addLogBreadcrumbs(' -- sendShipment -- ', $data);

        // 1. ASSERT SHIPMENT IS NOT DELETED, IN QUOTA AND STORE IS NOT DELETED
        $shipmentRepo = new ShipmentRepository();
        $isShipmentProcessable = $shipmentRepo->assetIsShipmentNotificationProcessable($shipmentId);

        if (! $isShipmentProcessable) {
            return null;
        }

        // 2. ASSERT THERE ARE ACTIVE CHANNEL RELATED TO THAT TRIGGER
        // 2.5 ASSET THAT CHANNEL SUPPORT THAT TRIGGER;
        $notificationConfigurationRepo = new NotificationConfigurationRepository();
        $notificationConfigs = $notificationConfigurationRepo->getAllActiveNotificationChannelsPerTrigger($storeId, $trigger);

        // 3. ASSERT IF CHANNEL IS PAID, THERE IS ENOUGH CHARGE
        // TODO

        // 4. ASSERT THAT NOTIFICATION AN BE RESENT
        // TODO

        $totalSendLogs = [];
        if (! is_null($notificationConfigs)) {
            $notifiable = new NotifiableBase();

            foreach ($notificationConfigs as $notificationConfigurationDTO) {

                /**
                 * @var NotificationTypeTriggerDTO $notificationTypeTriggerDTO
                 */
                foreach ($notificationConfigurationDTO->typeTriggers as $notificationTypeTriggerDTO) {
                    $messageDTO = self::_createIMessageDTO($notificationTypeTriggerDTO, $shipmentId, $checkpointId);

                    if (! is_null($messageDTO)) {
                        $withDelay = ! is_null($notificationTypeTriggerDTO->sentWithDelayMinutes);
                        if ($withDelay) {
                            $when = Carbon::now()->addMinutes($notificationTypeTriggerDTO->sentWithDelayMinutes);
                        } else {
                            $when = Carbon::now();
                        }

                        $notification = self::_createShipmentNotificationBase(
                            $notificationTypeTriggerDTO,
                            $notificationConfigurationDTO->config,
                            $messageDTO);
                        $notification->setLogData(null, $storeId, $shipmentId, $notificationTypeTriggerDTO->id, $checkpointId, $when->toDateTime());

                        // NB! read comments on _createShipmentNotificationBase
                        if ($withDelay && ($notification instanceof ShouldQueue)) {
                            $notifiable->notify($notification->delay($when));
                        } else {
                            $notifiable->notify($notification);
                        }

                        $totalSendLogs[] = $notification->getLogDTO();
                    }
                }
            }
        }

        if (count($totalSendLogs) == 0) {
            return null;
        }

        $storeDTO = StoreRepository::getSingle($storeId, false);
        $message = sprintf(
            '%s notifications (trigger on %s) for %s',
            implode(',', Arr::pluck($totalSendLogs, 'notificationChannel')),
            NotificationTriggerEnum::toSlug($trigger),
            $storeDTO->storeName
        );
        SentryService::dataMessage($message, SentryService::DEBUG, null, $totalSendLogs);

        return $totalSendLogs;
    }

    // SWITCH TO ALLOW YOU TO EASY work on local without Redis. DO NOT ABUSE AND ALWAYS TEST ON STAGE!!!! ~Slav
    private static function _createShipmentNotificationBase(NotificationTypeTriggerDTO $notificationTypeTriggerDTO, NotifConfigBaseDTO $config, IMessageDataDTO $messageDTO): ShipmentNotificationBase
    {
        if (true && App::environment(EnvironmentEnum::LOCAL()->value)) {
            // set this to true to send immediately notifications without queue
            return new ShipmentNotificationBase(
                            $notificationTypeTriggerDTO->trigger,
                            $notificationTypeTriggerDTO->notificationChannel,
                            $config,
                            $messageDTO);
        }

        return new ShipmentNotificationBaseInQueue(
                            $notificationTypeTriggerDTO->trigger,
                            $notificationTypeTriggerDTO->notificationChannel,
                            $config,
                            $messageDTO);
    }

    /// Used when we want to sent a test notification but we do not have full configuration.
    ///
    public static function sendTestShipment(
        NotifConfigBaseDTO $notificationConfigurationDTO,
        NotificationTypeTriggerDTO $notificationTypeTriggerDTO,
        int $storeId): ?NotificationLogDTO
    {
        $notification = null;
        $shipmentRepo = new ShipmentRepository();
        $shipmentId = $shipmentRepo->getRandomShipmentThatIsNotificationProcessable($storeId);

        if (! is_null($shipmentId)) {
            $messageDTO = self::_createIMessageDTO($notificationTypeTriggerDTO, $shipmentId, null);
            if (is_null($messageDTO)) {
                return null;
            }

            // Do not put Test into Queue, as we will not be able to return immediate success/fail response.
            $notification = new ShipmentNotificationBase(
                $notificationTypeTriggerDTO->trigger,
                $notificationTypeTriggerDTO->notificationChannel,
                $notificationConfigurationDTO,
                $messageDTO);

            $when = Carbon::now();
            $notification->setLogData(null, $storeId, $shipmentId, $notificationTypeTriggerDTO->id, null, $when->toDateTime());

            $notifiable = new NotifiableBase();
            $notifiable->notifyNow($notification);
        }

        if (is_null($notification)) {
            return null;
        }

        return $notification->getLogDTO();
    }

    private static function _createIMessageDTO(NotificationTypeTriggerDTO $triggerDTO, int $shipmentId, ?int $checkpointId): ?IMessageDataDTO
    {
        if ($triggerDTO->notificationChannel->equals(NotificationChannelEnum::SLACK())) {
            return ShipmentNotificationRepository::getSlackMessageDataDTODefault($shipmentId, $triggerDTO->trigger);
        } elseif ($triggerDTO->notificationChannel->equals(NotificationChannelEnum::KLAVIYO())) {
            return ShipmentNotificationRepository::getKlaviyoMessageDataDTODefault($shipmentId, $checkpointId);
        } elseif ($triggerDTO->notificationChannel->equals(NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT())) {
            $fulMsgDTO = ShipmentNotificationRepository::getShopifyFulfilmentMessageDataDTODefault($shipmentId, $checkpointId);
            if (! is_null($fulMsgDTO) && $fulMsgDTO->isValidShopifyFulfilmentEventStatus()) {
                return $fulMsgDTO;
            }

            return null;
        }

        return null;
    }
}

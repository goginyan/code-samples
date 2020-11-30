<?php

declare(strict_types=1);

namespace Notifications\Notifications\Channels;

use App\Services\SentryService;
use Carbon\Carbon;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Notifications\ShipmentNotificationBase;
use Notifications\Repositories\NotificationLogRepository;

abstract class BaseChannel
{
    protected ShipmentNotificationBase $_notification;

    public function __construct()
    {
    }

    public function send($notifiable, ShipmentNotificationBase $notification)
    {
        SentryService::addLogBreadcrumbs('BaseChannel::send(...)', $notification->toArray());
        $this->_notification = $notification;
        $this->log();
    }

    private function log(): void
    {
        $logDTO = $this->_notification->getLogDTO();
        if (! is_null($logDTO)) {
            $logDTO = NotificationLogRepository::create($this->_notification->getLogDTO());
            $this->_notification->setLogData(
                $logDTO->id,
                $logDTO->storeID,
                $logDTO->shipmentID,
                $logDTO->notificationTypeTriggerID,
                $logDTO->newestShipmentsHistoryInfoID,
                $logDTO->executeAfter);
            //int $storeID, int $shipmentID, int $notificationTypeTriggerID, ?int $checkpointID, DateTime $executeAfter
        }
    }

    public function markCompleted(NotificationLogStatusEnum $statusEnum): void
    {
        $logDTO = $this->_notification->getLogDTO();

        if (! is_null($logDTO)) {
            // Note that this if is because markCompleted is executed in a cron job, so we need to see how exactly to update this.
            // As $logDTO is null.....
            $logDTO->completedAt = Carbon::now()->toDateTime();
            $logDTO->status = $statusEnum;

            NotificationLogRepository::updateStatus($logDTO);
        }
    }
}

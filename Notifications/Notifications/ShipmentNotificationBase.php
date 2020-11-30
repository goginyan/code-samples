<?php

declare(strict_types=1);

namespace Notifications\Notifications;

use App\Enums\Job\QueueEnum;
use Carbon\Carbon;
use DateTime;
use Illuminate\Notifications\Notification;
use Notifications\DataTransferObjects\Models\NotificationLogDTO;
use Notifications\DataTransferObjects\NotifChannelMessageData\IMessageDataDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigBaseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Notifications\Channels\KlaviyoChannel;
use Notifications\Notifications\Channels\ShopifyFulfillmentChannel;
use Notifications\Notifications\Channels\SlackChannel;

class ShipmentNotificationBase extends Notification
{
    public NotificationTriggerEnum $trigger;
    public NotificationChannelEnum $channel;
    public NotifConfigBaseDTO $config;
    public IMessageDataDTO $dto;
    private ?NotificationLogDTO $_logDTO = null;

    public function __construct(
        NotificationTriggerEnum $trigger,
        NotificationChannelEnum $channel,
        NotifConfigBaseDTO $config,
        IMessageDataDTO $dto)
    {
        $this->trigger = $trigger;
        $this->channel = $channel;
        $this->config = $config;
        $this->dto = $dto;
    }

    public function toArray(): array
    {
        return [
            'trigger' => NotificationTriggerEnum::toSlug($this->trigger),
            'channel' => $this->channel->value,
            'config' => $this->config->toArray(),
            'dto' => $this->dto->toArray(),
            'log' => $this->_logDTO ? $this->_logDTO->toArray() : null,
        ];
    }

    public function setLogData(?int $id, int $storeID, int $shipmentID, int $notificationTypeTriggerID, ?int $checkpointID, DateTime $executeAfter): void
    {
        $this->_logDTO = new NotificationLogDTO(
                $id,
                $this->id,
                null,
                $this->trigger,
                $this->channel,
                $storeID,
                $shipmentID,
                $notificationTypeTriggerID,
                $checkpointID,
                null,
                null,
                $executeAfter,
                Carbon::now()->toDateTime(),
                null);
    }

    public function viaQueues(): array
    {
        return [
            NotificationChannelEnum::EMAIL_POSTMARKAPP()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::SMS_PLIVO()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::SLACK()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::KLAVIYO()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::PAYPAL()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::CUSTOM_WEBHOOKS()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT()->value => QueueEnum::NOTIFICATIONS()->value,
            NotificationChannelEnum::ZAPIER()->value => QueueEnum::NOTIFICATIONS()->value,
        ];
    }

    public function getLogDTO(): ?NotificationLogDTO
    {
        if (is_null($this->_logDTO)) {
            return null;
        }

        $this->_logDTO->notificationGUID = $this->id;

        return $this->_logDTO;
    }

    public function via($notifiable): array
    {
        // Based on the channel sent in, it returns that channel class so Laravel can push it to it.
        // List of channel are mandatory for the notification to be sent to.
        if ($this->channel->equals(NotificationChannelEnum::SLACK())) {
            return [SlackChannel::class];
        } elseif ($this->channel->equals(NotificationChannelEnum::KLAVIYO())) {
            return [KlaviyoChannel::class];
        } elseif ($this->channel->equals(NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT())) {
            return [ShopifyFulfillmentChannel::class];
        }

        return [];
    }
}

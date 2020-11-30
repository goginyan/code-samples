<?php

namespace Notifications\DataTransferObjects\Models;

use Data\DataTransferObjects\ArrayableDTO;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigBaseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Models\NotificationConfiguration;

class NotificationConfigurationDTO extends ArrayableDTO
{
    use ArrayableElements;

    public ?int $id;
    public int $storeID;
    public NotificationChannelEnum $notificationChannel;
    public ?bool $isActive;
    public NotifConfigBaseDTO $config;

    /**
     * @var NotificationTypeTriggerDTO[]|null
     */
    public ?array $typeTriggers = null;

    public function __construct(
                    ?int $id,
                    int $storeID,
                    NotificationChannelEnum $notificationChannel,
                    ?bool $isActive,
                    NotifConfigBaseDTO $config)
    {
        $this->id = $id;
        $this->storeID = $storeID;
        $this->notificationChannel = $notificationChannel;
        $this->isActive = $isActive;
        $this->config = $config;
    }

    public function toArray(): array
    {
        $out = [
            'id' => $this->id,
            'storeID' => $this->storeID,
            'notification_channel_slug' => $this->notificationChannel->value,
            'is_active' => $this->isActive,
            'config' => $this->config ? $this->config->toArray() : null,
        ];
        if (! is_null($this->typeTriggers)) {
            $out['type_triggers'] = $this->toArrayElements($this->typeTriggers);
        }

        return array_filter($out);
    }

    /**
     * @param NotificationConfiguration $model
     * @return NotificationConfigurationDTO
     */
    public static function fromModel(NotificationConfiguration $model): self
    {
        return new self(
                $model->id,
                $model->store_id,
                $model->notificationChannelEnum(),
                $model->is_active,
                $model->getJsonConfigAttribute());
    }

    public function toEloquentUpdate(): array
    {
        return [
            'store_id' => $this->storeID,
            'notification_channel_id' => NotificationChannelEnum::fromSlugToID($this->notificationChannel),
            'is_active' => $this->isActive,
            'json_config' => $this->config,
        ];
    }

    public function getNotificationChannelID()
    {
        return NotificationChannelEnum::fromSlugToID($this->notificationChannel);
    }
}

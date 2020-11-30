<?php

namespace Notifications\DataTransferObjects\Models;

use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;
use Notifications\DataTransferObjects\NotifTypeTriggerConfig\NotifTypeTriggerConfigBaseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Models\NotificationTypeTrigger;
use Notifications\Repositories\NotificationTriggersRepository;

class NotificationTypeTriggerDTO implements Arrayable
{
    use Translatable;

    public ?int $id;
    public ?int $storeId;
    public ?int $notificationConfigurationId;
    public ?NotificationChannelEnum $notificationChannel;
    public ?NotificationTriggerEnum $trigger;
    public ?bool $isActive;
    public ?TranslationDTO $activeLabel;
    public TranslationDTO $summary;
    public ?string $filterDurationLabel;
    public ?int $filterDurationMinutes;
    public ?string $sentWithDelayLabel;
    public ?int $sentWithDelayMinutes;

    public ?array $filterStatus;
    public ?array $filterSubstatus;

    public ?NotifTypeTriggerConfigBaseDTO $config;

    /**
     * @var TranslationDTO[]|null
     */
    public ?array $shipmentStatuses;
    public ?TranslationDTO $triggerTranslation;

    public function __construct(
        ?int $id,
        ?int $storeId,
        ?int $notificationConfigurationId,
        ?NotificationChannelEnum $notificationChannel,
        ?NotificationTriggerEnum $trigger,
        ?bool $isActive,
        ?string $filterDurationLabel,
        ?int $filterDurationMinutes,
        ?string $sentWithDelayLabel,
        ?int $sentWithDelayMinutes,
        ?array $filterStatus,
        ?array $filterSubstatus,
        ?NotifTypeTriggerConfigBaseDTO $config
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->notificationConfigurationId = $notificationConfigurationId;
        $this->notificationChannel = $notificationChannel;
        $this->trigger = $trigger;
        $this->isActive = $isActive;
        $this->filterDurationLabel = $filterDurationLabel;
        $this->filterDurationMinutes = $filterDurationMinutes;
        $this->sentWithDelayLabel = $sentWithDelayLabel;
        $this->sentWithDelayMinutes = $sentWithDelayMinutes;
        $this->filterStatus = $filterStatus;
        $this->filterSubstatus = $filterSubstatus;
        $this->config = $config;

        if ($isActive) {
            $this->activeLabel = new TranslationDTO('email.trigger_active_status.actived');
        } else {
            $this->activeLabel = new TranslationDTO('email.trigger_active_status.deactivated');
        }
        $this->summary = new TranslationDTO('email.trigger_summary');

        $this->shipmentStatuses = [];

        if ($filterStatus) {
            foreach ($filterStatus as $status) {
                $this->shipmentStatuses[] = new TranslationDTO('status.'.$status->value);
            }
        }

        if ($trigger) {
            $this->triggerTranslation = NotificationTriggersRepository::getTriggerTranslation($trigger);
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'store_id' => $this->storeId,
            'notification_configuration_id' => $this->notificationConfigurationId,
            'notification_channel_slug' => $this->notificationChannel->value,
            'trigger' => NotificationTriggerEnum::toSlug($this->trigger),
            'is_active' => $this->isActive,
            'filter_duration_label' => $this->filterDurationLabel,
            'filter_duration_minutes' => $this->filterDurationMinutes,
            'sent_with_delay_label' => $this->sentWithDelayLabel,
            'sent_with_delay_minutes' => $this->sentWithDelayMinutes,
            'filter_status' => $this->filterStatus,
            'filter_substatus' => $this->filterSubstatus,
            'config' => $this->config ? $this->config->toArray() : null,
        ]);
    }

    /**
     * @param NotificationTypeTrigger $model
     * @return self
     */
    public static function fromModel(NotificationTypeTrigger $model): self
    {
        return new self(
                    $model->id,
                    $model->store_id,
                    $model->notification_configuration_id,
                    NotificationChannelEnum::fromIDToSlug($model->notification_channel_id),
                    NotificationTriggerEnum::make($model->trigger_id),
                    $model->is_active,
                    $model->filter_duration_label,
                    $model->filter_duration_minutes,
                    $model->sent_with_delay_label,
                    $model->sent_with_delay_minutes,
                    $model->filter_status,
                    $model->filter_substatus,
                    $model->getJsonConfigAttribute()
        );
    }

    public function toEloquentUpdate(): array
    {
        return array_filter([
            'store_id' => $this->storeId,
            'notification_configuration_id' => $this->notificationConfigurationId,
            'notification_channel_id' => NotificationChannelEnum::fromSlugToID($this->notificationChannel),
            'trigger_id' => $this->trigger->value,
            'is_active' => $this->isActive,
            'filter_duration_label' => $this->filterDurationLabel,
            'filter_duration_minutes' => $this->filterDurationMinutes,
            'sent_with_delay_label' => $this->sentWithDelayLabel,
            'sent_with_delay_minutes' => $this->sentWithDelayMinutes,
            'filter_status' => $this->filterStatus,
            'filter_substatus' => $this->filterSubstatus,
            'json_config' => $this->config,
        ]);
    }

    private function getShipmentStatusesAsString(): string
    {
        $statusesArray = [];
        foreach ($this->shipmentStatuses as $status) {
            $statusesArray[] = $status->getText();
        }

        return implode(', ', $statusesArray);
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        if ($this->summary->isTranslated()) {
            return str_replace(['{{trigger_translation}}', '{{shipment_statuses}}'], ['**'.$this->triggerTranslation->getText().'**', '**'.$this->getShipmentStatusesAsString().'**'], $this->summary->getText());
        }

        return null;
    }
}

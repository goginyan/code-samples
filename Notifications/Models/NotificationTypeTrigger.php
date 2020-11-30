<?php

namespace Notifications\Models;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\DataTransferObjects\NotifTypeTriggerConfig\NotifTypeTriggerConfigBaseDTO;
use Notifications\DataTransferObjects\NotifTypeTriggerConfig\NotifTypeTriggerConfigConfigEmailDTO;
use Notifications\DataTransferObjects\NotifTypeTriggerConfig\NotifTypeTriggerConfigConfigSMSDTO;
use Notifications\Enums\NotificationChannelEnum;
use Shipments\Enums\ShipmentStatusEnum;

class NotificationTypeTrigger extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'store_id',
        'notification_configuration_id',
        'notification_channel_id',
        'trigger_id',
        'is_active',
        'filter_duration_label',
        'filter_duration_minutes',
        'sent_with_delay_label',
        'sent_with_delay_minutes',
        'filter_status',
        'filter_substatus',
        'json_config',
    ];

    /**
     * @param $value
     * @return NotifTypeTriggerConfigBaseDTO
     */
    public function getJsonConfigAttribute(): ?NotifTypeTriggerConfigBaseDTO
    {
        /// when not selected -> return null.
        if (! isset($this->attributes['json_config'])) {
            return null;
        }

        $notificationChannelEnum = NotificationChannelEnum::fromIDToSlug($this->notification_channel_id);

        return self::fromJSONtoDTO(json_decode($this->attributes['json_config'], true), $notificationChannelEnum);
    }

    /**
     * @param DatabaseJsonInterface $value
     */
    public function setJsonConfigAttribute(?DatabaseJsonInterface $value): void
    {
        $this->attributes['json_config'] = $value ? $value->toDatabaseJSON() : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configuration()
    {
        return $this->belongsTo(NotificationConfiguration::class, 'notification_configuration_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo#
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trigger()
    {
        return $this->belongsTo(NotificationTrigger::class, 'trigger_id');
    }

    public static function fromJSONtoDTO(?array $json, NotificationChannelEnum $channelEnum): ?NotifTypeTriggerConfigBaseDTO
    {
        if (! $json) {
            return null;
        }

        if ($channelEnum->equals(NotificationChannelEnum::EMAIL_POSTMARKAPP())) {
            return NotifTypeTriggerConfigConfigEmailDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::SMS_PLIVO())) {
            return NotifTypeTriggerConfigConfigSMSDTO::fromDatabaseJSON($json);
        }

        return null;
    }

    public static function fromJSONArrayToShipmentStatuses(?array $json): ?array
    {
        if (empty($json)) {
            return null;
        }

        return $json;
    }

    public static function fromJSONArrayToShipmentSubStatuses(?array $json): ?array
    {
        if (empty($json)) {
            return null;
        }

        return $json;
    }

    /**
     * @param $value
     * @return ShipmentStatusEnum[]
     */
    public function getFilterStatusAttribute(?string $value): array
    {
        $statusesArray = array_filter(explode(',', str_replace(['{', '}', '"'], '', $value)));
        $statusDTOArray = [];

        foreach ($statusesArray as $status) {
            $statusDTOArray[] = ShipmentStatusEnum::make($status);
        }

        return $statusDTOArray;
    }

    /**
     * @param ShipmentStatusEnum[] $data
     */
    public function setFilterStatusAttribute(array $data): void
    {
        $arrayData = [];

        foreach ($data as $enum) {
            $arrayData[] = $enum->value;
        }

        $this->attributes['filter_status'] = str_replace(['[', ']'], ['{', '}'], json_encode($arrayData));
    }
}

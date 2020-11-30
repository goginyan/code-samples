<?php

namespace Notifications\Models;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigBaseDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigCustomWebhookDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigEmailPostmarkAppDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigFBMessengerDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigKlaviyoDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigPayPalDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigShopifyFulfillmentEventDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigSlackDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigSMSPilvoDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigZappierDTO;
use Notifications\Enums\NotificationChannelEnum;

class NotificationConfiguration extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'store_id',
        'notification_channel_id',
        'json_config',
    ];

    public function getJsonConfigAttribute(): ?NotifConfigBaseDTO
    {
        $notificationChannelEnum = NotificationChannelEnum::fromIDToSlug($this->notification_channel_id);

        return self::fromJSONtoDTO(json_decode($this->attributes['json_config'], true), $notificationChannelEnum);
    }

    /**
     * @param DatabaseJsonInterface $value
     */
    public function setJsonConfigAttribute(DatabaseJsonInterface $value): void
    {
        $this->attributes['json_config'] = $value ? $value->toDatabaseJSON() : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }

    public function notificationChannelEnum(): NotificationChannelEnum
    {
        return NotificationChannelEnum::fromIDToSlug($this->notification_channel_id);
    }

    public static function fromJSONtoDTO(array $json, NotificationChannelEnum $channelEnum): ?NotifConfigBaseDTO
    {
        if ($channelEnum->equals(NotificationChannelEnum::EMAIL_POSTMARKAPP())) {
            return NotifConfigEmailPostmarkAppDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::SLACK())) {
            return NotifConfigSlackDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::KLAVIYO())) {
            return NotifConfigKlaviyoDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::PAYPAL())) {
            return NotifConfigPayPalDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::SMS_PLIVO())) {
            return NotifConfigSMSPilvoDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::CUSTOM_WEBHOOKS())) {
            return NotifConfigCustomWebhookDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::ZAPIER())) {
            return NotifConfigZappierDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT())) {
            return NotifConfigShopifyFulfillmentEventDTO::fromDatabaseJSON($json);
        } elseif ($channelEnum->equals(NotificationChannelEnum::FB_MESSENGER_BOT())) {
            return NotifConfigFBMessengerDTO::fromDatabaseJSON($json);
        }

        return null;
    }

    public static function getDefaultConfigForChannel(NotificationChannelEnum $channelEnum): ?NotifConfigBaseDTO
    {
        if ($channelEnum->equals(NotificationChannelEnum::EMAIL_POSTMARKAPP())) {
            return NotifConfigEmailPostmarkAppDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::SLACK())) {
            return NotifConfigSlackDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::KLAVIYO())) {
            return NotifConfigKlaviyoDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::PAYPAL())) {
            return NotifConfigPayPalDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::SMS_PLIVO())) {
            return NotifConfigSMSPilvoDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::CUSTOM_WEBHOOKS())) {
            return NotifConfigCustomWebhookDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::ZAPIER())) {
            return NotifConfigZappierDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT())) {
            return NotifConfigShopifyFulfillmentEventDTO::getDefault();
        } elseif ($channelEnum->equals(NotificationChannelEnum::FB_MESSENGER_BOT())) {
            return NotifConfigFBMessengerDTO::getDefault();
        }

        return null;
    }
}

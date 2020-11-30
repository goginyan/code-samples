<?php

namespace Notifications\Enums;

use Data\Enums\FeatureEnum;
use Spatie\Enum\Enum;

/**
 * @method static self EMAIL_POSTMARKAPP()
 * @method static self SMS_PLIVO()
 * @method static self SLACK()
 * @method static self KLAVIYO()
 * @method static self PAYPAL()
 * @method static self CUSTOM_WEBHOOKS()
 * @method static self SHOPIFY_FULFILMENT_EVENT()
 * @method static self ZAPIER()
 * @method static self FB_MESSENGER_BOT()
 */
class NotificationChannelEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'EMAIL_POSTMARKAPP' => 'email_postmarkapp',
            'SMS_PLIVO' => 'sms_plivo',
            'SLACK' => 'slack',
            'KLAVIYO' => 'klaviyo',
            'PAYPAL' => 'paypal',
            'CUSTOM_WEBHOOKS' => 'custom_webhooks',
            'SHOPIFY_FULFILMENT_EVENT' => 'shopify_fulfilment_event',
            'ZAPIER' => 'zapier',
            'FB_MESSENGER_BOT' => 'fb_messenger_bot',
        ];
    }

    public static function fromIDToSlug(int $id): ?self
    {
        if ($id == 1) {
            return self::EMAIL_POSTMARKAPP();
        } elseif ($id == 2) {
            return self::SMS_PLIVO();
        } elseif ($id == 3) {
            return self::SLACK();
        } elseif ($id == 4) {
            return self::KLAVIYO();
        } elseif ($id == 5) {
            return self::PAYPAL();
        } elseif ($id == 6) {
            return self::CUSTOM_WEBHOOKS();
        } elseif ($id == 7) {
            return self::SHOPIFY_FULFILMENT_EVENT();
        } elseif ($id == 8) {
            return self::ZAPIER();
        } elseif ($id == 9) {
            return self::FB_MESSENGER_BOT();
        }

        return null;
    }

    public static function fromSlugToID(self $channelEnum): ?int
    {
        if ($channelEnum->equals(self::EMAIL_POSTMARKAPP())) {
            return 1;
        } elseif ($channelEnum->equals(self::SMS_PLIVO())) {
            return 2;
        } elseif ($channelEnum->equals(self::SLACK())) {
            return 3;
        } elseif ($channelEnum->equals(self::KLAVIYO())) {
            return 4;
        } elseif ($channelEnum->equals(self::PAYPAL())) {
            return 5;
        } elseif ($channelEnum->equals(self::CUSTOM_WEBHOOKS())) {
            return 6;
        } elseif ($channelEnum->equals(self::SHOPIFY_FULFILMENT_EVENT())) {
            return 7;
        } elseif ($channelEnum->equals(self::ZAPIER())) {
            return 8;
        } elseif ($channelEnum->equals(self::FB_MESSENGER_BOT())) {
            return 9;
        }

        return null;
    }

    public static function toFeatureEnum(self $channelEnum): ?FeatureEnum
    {
        if ($channelEnum->equals(self::EMAIL_POSTMARKAPP())) {
            return FeatureEnum::EMAIL();
        } elseif ($channelEnum->equals(self::SMS_PLIVO())) {
            return FeatureEnum::SMS();
        } elseif ($channelEnum->equals(self::SLACK())) {
            return FeatureEnum::SLACK();
        } elseif ($channelEnum->equals(self::KLAVIYO())) {
            return FeatureEnum::KLAVIYO();
        } elseif ($channelEnum->equals(self::PAYPAL())) {
            return FeatureEnum::PAYPAL();
        } elseif ($channelEnum->equals(self::CUSTOM_WEBHOOKS())) {
            return FeatureEnum::WEBHOOKS();
        } elseif ($channelEnum->equals(self::SHOPIFY_FULFILMENT_EVENT())) {
            return FeatureEnum::SHOPIFY_FULFILMENT_EVENT();
        } elseif ($channelEnum->equals(self::ZAPIER())) {
            return FeatureEnum::ZAPIER();
        } elseif ($channelEnum->equals(self::FB_MESSENGER_BOT())) {
            return FeatureEnum::FB_MESSENGER_BOT();
        }

        return null;
    }

    public static function fromFeatureEnum(FeatureEnum $featureEnum): ?self
    {
        if ($featureEnum->equals(FeatureEnum::EMAIL())) {
            return self::EMAIL_POSTMARKAPP();
        } elseif ($featureEnum->equals(FeatureEnum::SMS())) {
            return self::SMS_PLIVO();
        } elseif ($featureEnum->equals(FeatureEnum::SLACK())) {
            return self::SLACK();
        } elseif ($featureEnum->equals(FeatureEnum::KLAVIYO())) {
            return self::KLAVIYO();
        } elseif ($featureEnum->equals(FeatureEnum::PAYPAL())) {
            return self::PAYPAL();
        } elseif ($featureEnum->equals(FeatureEnum::WEBHOOKS())) {
            return self::CUSTOM_WEBHOOKS();
        } elseif ($featureEnum->equals(FeatureEnum::SHOPIFY_FULFILMENT_EVENT())) {
            return self::SHOPIFY_FULFILMENT_EVENT();
        } elseif ($featureEnum->equals(FeatureEnum::ZAPIER())) {
            return self::ZAPIER();
        } elseif ($featureEnum->equals(FeatureEnum::FB_MESSENGER_BOT())) {
            return self::FB_MESSENGER_BOT();
        }

        return null;
    }
}

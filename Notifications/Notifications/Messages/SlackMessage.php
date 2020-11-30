<?php

declare(strict_types=1);

namespace Notifications\Notifications\Messages;

use Data\Enums\LanguageEnum;
use Notifications\DataTransferObjects\NotifChannelMessageData\SlackMessageDataDTO;
use Notifications\Enums\NotificationTriggerEnum;
use Store\Services\GAUTMBuilderService;

class SlackMessage
{
    public static function DEFAULT_MESSAGE(SlackMessageDataDTO $dto, LanguageEnum $language, NotificationTriggerEnum $triggerEnum): array
    {
        $dto->translate(LanguageEnum::toSlug($language));
        // top text
        $sectionHeadingText = null;
        if (is_null($dto->orderAdminLink)) {
            $sectionHeadingText = $dto->orderID;
        } else {
            $orderAdminLink = self::_applyGAUTMBuilderService($dto->orderAdminLink, $dto, $triggerEnum);
            $sectionHeadingText = '_'.$dto->trigger->getText().'_ event for <'.$orderAdminLink.'|#'.$dto->orderID.'>';
        }

        $sectionHeadingText .= ' *'.$dto->shipmentStatus->getText().'* ';
        if (! is_null($dto->shipmentSubStatus)) {
            $sectionHeadingText .= ' (*'.$dto->shipmentSubStatus->getText().'*) ';
        }
        $sectionHeadingText .= "\n";

        $addressArray = array_filter([$dto->shippingAddressAddress, $dto->shippingAddressCity, $dto->shippingAddressZIP, $dto->shippingAddressCountry], fn ($value) => ! is_null($value) && $value !== '');

        if (! empty($addressArray)) {
            $shipping_address = implode(', ', $addressArray);
            $sectionHeadingText .= '@ '.$shipping_address.' ';
        }

        if (! is_null($dto->lastCheckpointDescription)) {
            $sectionHeadingText .= '*'.$dto->lastCheckpointDescription.'*';
        }

        // last line;
        $carrierLink = null;
        if (is_null($dto->courierTrackingLink)) {
            $carrierLink = $dto->courierName;
            if (! is_null($dto->courierTrackingNumber)) {
                $carrierLink = ' - '.$dto->courierTrackingNumber;
            }
        } else {
            $courierTrackingLink = self::_applyGAUTMBuilderService($dto->courierTrackingLink, $dto, $triggerEnum);
            $carrierLink = '<'.$courierTrackingLink.'|'.$dto->courierName.' - '.$dto->courierTrackingNumber.'>';
        }

        $trackingLink = self::_applyGAUTMBuilderService($dto->trackingLink, $dto, $triggerEnum);

        $payload = [
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $sectionHeadingText,
                    ],
                    'accessory' => [
                        'type' => 'image',
                        'image_url' => $dto->shipmentImageURL,
                        'alt_text' => $dto->shipmentTitle,
                    ],
                ],
                [
                    'type' => 'context',
                    'elements' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => '<'.$trackingLink.'|'.$dto->trackingNumber.'>',
                        ],
                        [
                            'type' => 'image',
                            'image_url' => $dto->courierLogoNonSVG,
                            'alt_text' => 'logo',
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => ' 🚚 '.$carrierLink,
                        ],
                    ],
                ],
            ],
        ];

        return $payload;
    }

    /**
     * Apply Google Analytics utm tags.
     * @param string|null $value
     * @param SlackMessageDataDTO $dto
     * @param NotificationTriggerEnum $triggerEnum
     * @return string|null
     */
    private static function _applyGAUTMBuilderService(?string $value, SlackMessageDataDTO $dto, NotificationTriggerEnum $triggerEnum): ?string
    {
        if (! is_null($value)) {
            return GAUTMBuilderService::notificationChannel(
                $value,
                $dto->shipmentStatusSlug,
                $triggerEnum,
                $dto->getChannel()
            );
        }

        return null;
    }
}

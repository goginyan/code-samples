<?php

namespace Notifications\Services;

use App\DataTransferObjects\Responses\ErrorResponseDTO;
use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use App\Errors\ErrorsIds;
use App\Repositories\BillingPlanRepository;
use App\Services\SentryService;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Data\DataTransferObjects\Translations\TranslationFindAndReplaceDTO;
use Data\Enums\LanguageEnum;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigSlackDTO;
use Notifications\DataTransferObjects\Responses\NotificationTestResponseDTO;
use Notifications\DataTransferObjects\Responses\SlackConfigResponseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Repositories\NotificationConfigurationRepository;
use Notifications\Repositories\NotificationTypeTriggersRepository;
use Shipments\Repositories\StoreRepository;

class SlackService
{
    /**
     * Get list of possible Slack Notification Triggers.
     * @return NotificationTriggerEnum[]
     */
    public static function getPossibleTriggers(): array
    {
        return [
            NotificationTriggerEnum::NEW_CHECKPOINT(),
            NotificationTriggerEnum::NEW_SHIPMENT(),
            NotificationTriggerEnum::FORECASTED_POSSIBLE_DELAY(),

        ];
    }

    /**
     * Return supported languages for webhooks.
     * @return LanguageEnum[]
     */
    public static function getPossibleLanguages(): array
    {
        return [
            LanguageEnum::EN(),
            LanguageEnum::DE(),
            LanguageEnum::FR(),
        ];
    }

    private static function _validateWebhookURL($url): bool
    {
        return preg_match('/^https:\/\/hooks.slack.com\/services\/.*\/.*/i', $url) == 1;
    }

    public static function setConfiguration(?int $storeID, ?int $id, ?string $webhookURL, ?string $triggerSlug, ?string $language): ResponseDTO
    {
        //
        // CHECKS
        //
        //

        if (! self::_validateWebhookURL($webhookURL)) {
            $error = new ErrorResponseDTO(
                HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(),
                ErrorsIds::SLACK_WEBHOOK_URL_INVALID,
                'Webhook not recognized as slack webhook',
                new TranslationDTO('error.slack_webhook_url_invalid.title'),
                new TranslationDTO('error.slack_webhook_url_invalid.description', null, [
                    new TranslationFindAndReplaceDTO('{{inserted_webhook}}', $webhookURL),
                ]),
                null
            );
            $error->translate();

            return $error;
        }

        $languageEnum = LanguageEnum::fromSlug($language);
        if (is_null($languageEnum)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::LANGUAGE_INVALID, true);
        }

        // TODO add a check that language is in the list of permited languags;

        $notificationTriggerEnum = NotificationTriggerEnum::fromSlug($triggerSlug);
        if (is_null($notificationTriggerEnum)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::INVALID_NOTIFICATION_TRIGGER, true);
        }

        // TODO add a check that trigger is in the list of permited triggers;

        if (is_null($storeID)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::SLACK();
        $featureEnum = NotificationChannelEnum::toFeatureEnum($notificationChannelEnum);

        $billingPlanRepo = new BillingPlanRepository();
        $featureSupported = $billingPlanRepo->isFeatureInActiveBilling($storeID, $featureEnum);

        if (! $featureSupported) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_PAYMENT_REQUIRED_402(), ErrorsIds::FEATURE_NOT_AVAILABLE);
        }

        //
        // ACTUAL BUSINESS LOGIC
        //
        //

        $repo = new NotificationConfigurationRepository();
        $triggerDTO = null;
        $config = null;

        $notificationConfigurationDTO = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);

        if (is_null($notificationConfigurationDTO)) {
            SentryService::dataMessage('Notification channel missing for Slack', SentryService::FATAL, null, [
                'store_id' => $storeID,
                'id' => $id,
                'webhook_url' => $webhookURL,
                'trigger_slug' => $triggerSlug,
                'language' => $language,
            ]);

            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::MISSING_NOTIFICATION_CHANNEL_CONFIGURATION, true);
        } else {
            // Should return max 1;
            $config = new NotifConfigSlackDTO();
            $config->language = $languageEnum;
            $config->webhookURL = $webhookURL;

            $notificationConfigurationDTO->config = $config;

            $repo->update($notificationConfigurationDTO);
            /**
             * @var NotificationTypeTriggerDTO[]|null $typeTriggers
             */
            $typeTriggers = $notificationConfigurationDTO->typeTriggers;

            if (is_null($typeTriggers)) {
                // insert new record for Slack Type triggers
                $triggerDTO = new NotificationTypeTriggerDTO(
                    null,
                    $storeID,
                    $notificationConfigurationDTO->id,
                    $notificationChannelEnum,
                    $notificationTriggerEnum,
                    true,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                );

                $triggerDTO = $repo->updateOrInsertTypeTrigger($triggerDTO);
            } else {
                // update
                $typeTrigger = $typeTriggers[0];
                $typeTrigger->trigger = $notificationTriggerEnum;

                $triggerDTO = new NotificationTypeTriggerDTO(
                    is_null($id) ? $typeTrigger->id : $id,
                    $storeID,
                    $notificationConfigurationDTO->id,
                    $notificationChannelEnum,
                    $notificationTriggerEnum,
                    true,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                );
                $triggerDTO = $repo->updateOrInsertTypeTrigger($triggerDTO);
            }
        }

        $slackConfigResponseDTO = new SlackConfigResponseDTO(
            $triggerDTO->id,
            $config->webhookURL,
            $config->language,
            $triggerDTO->trigger
        );

        $slackConfigResponseDTO->translate();

        return $slackConfigResponseDTO;
    }

    public static function getConfiguration(?int $storeID): ResponseDTO
    {
        //
        // CHECKS
        //
        //

        // Store is not null
        if (is_null($storeID)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        // Store exist
        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::SLACK();
        $featureEnum = NotificationChannelEnum::toFeatureEnum($notificationChannelEnum);

        // Based on billing plan, features is available
        $billingPlanRepo = new BillingPlanRepository();
        $featureSupported = $billingPlanRepo->isFeatureInActiveBilling($storeID, $featureEnum);

        if (! $featureSupported) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_PAYMENT_REQUIRED_402(), ErrorsIds::FEATURE_NOT_AVAILABLE);
        }

        //
        // ACTUAL BUSINESS LOGIC
        //
        //

        $slackConfigResponseDTO = null;
        $repo = new NotificationConfigurationRepository();
        $notificationChannelDTO = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);

        if (! is_null($notificationChannelDTO)) {
            /**
             * @var NotifConfigSlackDTO $config
             */
            $config = $notificationChannelDTO->config;
            /**
             * @var NotificationTypeTriggerDTO[]|null $typeTriggers
             */
            $typeTriggers = $notificationChannelDTO->typeTriggers;

            if (! is_null($typeTriggers)) {
                $notificationTypeTriggerDTO = $typeTriggers[0];
                $slackConfigResponseDTO = new SlackConfigResponseDTO(
                    $notificationTypeTriggerDTO->id,
                    $config->webhookURL,
                    $config->language,
                    $notificationTypeTriggerDTO->trigger);
            }
        }

        if (is_null($slackConfigResponseDTO)) {
            $config = NotifConfigSlackDTO::getDefault();

            $slackConfigResponseDTO = new SlackConfigResponseDTO(
                null,
                $config->webhookURL,
                $config->language,
                NotificationTriggerEnum::NEW_CHECKPOINT());
        }

        $slackConfigResponseDTO->translate();

        return $slackConfigResponseDTO;
    }

    public static function sendTest(?int $storeID, ?string $webhookURL, ?string $triggerSlug, ?string $language): ResponseDTO
    {
        //
        // CHECKS
        //
        //

        if (! self::_validateWebhookURL($webhookURL)) {
            $error = new ErrorResponseDTO(
                HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(),
                ErrorsIds::SLACK_WEBHOOK_URL_INVALID,
                'Webhook not recognized as slack webhook',
                new TranslationDTO('error.slack_webhook_url_invalid.title'),
                new TranslationDTO('error.slack_webhook_url_invalid.description', null, [
                    new TranslationFindAndReplaceDTO('{{inserted_webhook}}', $webhookURL),
                ]),
                null
            );
            $error->translate();

            return $error;
        }

        $languageEnum = LanguageEnum::fromSlug($language);
        if (is_null($language)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::LANGUAGE_INVALID, true);
        }

        $notificationTriggerEnum = NotificationTriggerEnum::fromSlug($triggerSlug);
        if (is_null($notificationTriggerEnum)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::INVALID_NOTIFICATION_TRIGGER, true);
        }

        if (is_null($storeID)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::SLACK();
        $featureEnum = NotificationChannelEnum::toFeatureEnum($notificationChannelEnum);

        $billingPlanRepo = new BillingPlanRepository();
        $featureSupported = $billingPlanRepo->isFeatureInActiveBilling($storeID, $featureEnum);

        if (! $featureSupported) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_PAYMENT_REQUIRED_402(), ErrorsIds::FEATURE_NOT_AVAILABLE);
        }

        //
        // ACTUAL BUSINESS LOGIC
        //
        //

        $config = new NotifConfigSlackDTO();
        $config->webhookURL = $webhookURL;
        $config->language = $languageEnum;

        $notificationTypeTriggerDTO = NotificationTypeTriggersRepository::getTypeTrigger($storeID, $notificationTriggerEnum->value, NotificationChannelEnum::fromSlugToID($notificationChannelEnum));

        if (is_null($notificationTypeTriggerDTO)) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(), ErrorsIds::NOTIFICATION_TRIGGERS_INVALID, true);
        }

        $logDTO = NotificationManager::sendTestShipment($config, $notificationTypeTriggerDTO, $storeID);

        if (is_null($logDTO) || ($logDTO->status && NotificationLogStatusEnum::FAILED()->equals($logDTO->status))) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(), ErrorsIds::NOTIFICATION_NOT_SENT, true);
        }

        $response = new NotificationTestResponseDTO(new TranslationDTO('slack.modal.test_success'));
        $response->translate();

        return $response;
    }
}

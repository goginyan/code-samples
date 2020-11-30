<?php

declare(strict_types=1);

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
use Illuminate\Support\Str;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigKlaviyoDTO;
use Notifications\DataTransferObjects\Responses\KlaviyoConfigResponseDTO;
use Notifications\DataTransferObjects\Responses\NotificationTestResponseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Repositories\NotificationConfigurationRepository;
use Notifications\Repositories\NotificationTypeTriggersRepository;
use Shipments\Repositories\StoreRepository;

class KlaviyoService
{
    /**
     * Get list of possible Klaviyo Notification Triggers.
     * @return NotificationTriggerEnum[]
     */
    public static function getPossibleTriggers(): array
    {
        return [
            NotificationTriggerEnum::NEW_CHECKPOINT(),
            NotificationTriggerEnum::NEW_SHIPMENT(),
            NotificationTriggerEnum::SHIPMENT_NEW_STATUS(),
            NotificationTriggerEnum::FORECASTED_POSSIBLE_DELAY(),
            NotificationTriggerEnum::PACKAGE_IN_DESTINATION_COUNTRY(),
            NotificationTriggerEnum::PACKAGE_LEFT_ORIGIN_COUNTRY(),
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

    public static function validateAPIKey(string $publicAPIKey): bool
    {
        // TODO: validate that klaviyo public key is good
        if (Str::contains($publicAPIKey, 'pk_')) {
            // this is private key
            return false;
        }

        return true;
    }

    /**
     * @param int $storeID
     * @param int|null $id
     * @param string|null $publicAPIKey
     * @param NotificationTypeTriggerDTO[]|null $triggers
     * @param string|null $language
     * @return ResponseDTO
     */
    public static function setConfiguration(int $storeID, ?int $id, ?string $publicAPIKey, ?array $triggers, ?string $language): ResponseDTO
    {
        //
        // CHECKS
        //
        //
        if (! self::validateAPIKey($publicAPIKey)) {
            $error = new ErrorResponseDTO(
                HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(),
                ErrorsIds::KLAVIYO_API_KEY_INVALID,
                'Api key is invalid',
                new TranslationDTO('error.klaviyo_api_key_invalid.title'),
                new TranslationDTO('error.klaviyo_api_key_invalid.description', null, [
                    new TranslationFindAndReplaceDTO('{{inserted_api_key}}', $publicAPIKey),
                ]),
                null
            );
            $error->translate();

            return $error;
        }

        $languageEnum = LanguageEnum::fromSlug($language);
        if (is_null($languageEnum) || ! $languageEnum->equals(...self::getPossibleLanguages())) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::LANGUAGE_INVALID, true);
        }

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::KLAVIYO();
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
        $notificationChannels = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);
        if (is_null($notificationChannels)) {
            SentryService::dataMessage('Notification channel missing for Klaviyo', SentryService::FATAL, null, [
                'store_id' => $storeID,
                'id' => $id,
                'public_api_key' => $publicAPIKey,
                'trigger_slug' => $triggers,
                'language' => $language,
            ]);

            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::MISSING_NOTIFICATION_CHANNEL_CONFIGURATION, true);
        } else {
            $config = new NotifConfigKlaviyoDTO();
            $config->language = $languageEnum;
            $config->publicApiKey = $publicAPIKey;

            $notificationConfigurationDTO = $notificationChannels;
            $notificationConfigurationDTO->config = $config;
            $repo->update($notificationConfigurationDTO);

            $notificationChannelId = NotificationChannelEnum::fromSlugToID($notificationChannelEnum);

            $checkTriggers = NotificationTypeTriggersRepository::checkIfTypeTriggersExists(
                $storeID,
                $notificationConfigurationDTO->id,
                $notificationChannelId,
                $triggers
            );
            if (! $checkTriggers) {
                return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOTIFICATION_TRIGGERS_INVALID, true);
            }

            $channelWithTypeTriggers = NotificationTypeTriggersRepository::updateByTriggers(
                $storeID,
                $notificationConfigurationDTO->id,
                $notificationChannelEnum,
                $triggers
            );

            $typeTriggers = $channelWithTypeTriggers->typeTriggers;
        }

        $klaviyoConfigResponseDTO = new KlaviyoConfigResponseDTO(
            $notificationConfigurationDTO->id,
            $config->publicApiKey,
            $config->language,
            $typeTriggers
        );

        $klaviyoConfigResponseDTO->translate();

        return $klaviyoConfigResponseDTO;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function getConfiguration(int $storeID): ResponseDTO
    {
        //
        // CHECKS
        //
        //

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        // Store exist
        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::KLAVIYO();
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

        $klaviyoConfigResponseDTO = null;
        $repo = new NotificationConfigurationRepository();

        $notificationChannels = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);

        if (! is_null($notificationChannels)) {
            $notificationChannelDTO = $notificationChannels;
            /**
             * @var NotifConfigKlaviyoDTO $config
             */
            $config = $notificationChannelDTO->config;
            /**
             * @var NotificationTypeTriggerDTO[]|null $typeTriggers
             */
            $typeTriggers = $notificationChannelDTO->typeTriggers;

            if (! is_null($typeTriggers)) {
                $klaviyoConfigResponseDTO = new KlaviyoConfigResponseDTO(
                    $notificationChannelDTO->id,
                    $config->publicApiKey,
                    $config->language,
                    $typeTriggers
                );
            }
        }

        if (is_null($klaviyoConfigResponseDTO)) {
            $config = NotifConfigKlaviyoDTO::getDefault();

            $klaviyoConfigResponseDTO = new KlaviyoConfigResponseDTO(
                null,
                $config->publicApiKey,
                $config->language,
                null
            );
        }

        $klaviyoConfigResponseDTO->translate();

        return $klaviyoConfigResponseDTO;
    }

    /**
     * @param int $storeID
     * @param string|null $publicAPIKey
     * @param NotificationTypeTriggerDTO[]|null $triggers
     * @param string|null $language
     * @return ResponseDTO
     */
    public static function sendTest(int $storeID, ?string $publicAPIKey, ?array $triggers, ?string $language): ResponseDTO
    {
        //
        // CHECKS
        //
        //

        if (! self::validateAPIKey($publicAPIKey)) {
            $error = new ErrorResponseDTO(
                HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(),
                ErrorsIds::KLAVIYO_API_KEY_INVALID,
                'Api key is invalid',
                new TranslationDTO('error.klaviyo_api_key_invalid.title'),
                new TranslationDTO('error.klaviyo_api_key_invalid.description', null, [
                    new TranslationFindAndReplaceDTO('{{inserted_api_key}}', $publicAPIKey),
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

        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::KLAVIYO();
        $featureEnum = NotificationChannelEnum::toFeatureEnum($notificationChannelEnum);

        $billingPlanRepo = new BillingPlanRepository();
        $featureSupported = $billingPlanRepo->isFeatureInActiveBilling($storeID, $featureEnum);

        if (! $featureSupported) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_PAYMENT_REQUIRED_402(), ErrorsIds::FEATURE_NOT_AVAILABLE);
        }

        $repo = new NotificationConfigurationRepository();
        $triggerDTO = null;
        $config = null;

        $notificationChannels = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);

        if (is_null($notificationChannels)) {
            SentryService::dataMessage('Notification channel missing for Klaviyo (send test)', SentryService::FATAL, null, [
                'store_id' => $storeID,
                'public_api_key' => $publicAPIKey,
                'trigger_slug' => $triggers,
                'language' => $language,
            ]);

            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::MISSING_NOTIFICATION_CHANNEL_CONFIGURATION, true);
        }

        $notificationChannelId = NotificationChannelEnum::fromSlugToID($notificationChannelEnum);

        $checkTriggers = NotificationTypeTriggersRepository::checkIfTypeTriggersExists(
            $storeID,
            $notificationChannels->id,
            $notificationChannelId,
            $triggers
        );
        if (! $checkTriggers) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOTIFICATION_TRIGGERS_INVALID, true);
        }

        //
        // ACTUAL BUSINESS LOGIC
        //
        //

        $config = new NotifConfigKlaviyoDTO();
        $config->publicApiKey = $publicAPIKey;
        $config->language = $languageEnum;

        $data = [];
        $isError = false;
        foreach ($triggers as $trigger) {
            $trigger->notificationChannel = NotificationChannelEnum::KLAVIYO();
            $trigger->notificationConfigurationId = $notificationChannels->id;
            $logDTO = NotificationManager::sendTestShipment($config, $trigger, $storeID);
            if (is_null($logDTO) || ($logDTO->status && NotificationLogStatusEnum::FAILED()->equals($logDTO->status))) {
                $isError = true;
            } else {
                $data[] = $logDTO->toArray();
            }
        }

        if ($isError) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(), ErrorsIds::NOTIFICATION_NOT_SENT, true);
        } else {
            $response = new NotificationTestResponseDTO(new TranslationDTO('klaviyo.modal.test_success'));
            $response->translate();

            return $response;
        }
    }

    /**
     * @param int $storeID
     * @param int $configId
     * @param bool $isActive
     */
    public static function createDefaultTriggers(int $storeID, int $configId, bool $isActive = false): void
    {
        $triggers = self::getPossibleTriggers();

        NotificationTypeTriggersRepository::createKlaviyoTriggersIfNotExists($storeID, $configId, $triggers, $isActive);
    }
}

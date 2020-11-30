<?php

declare(strict_types=1);

namespace Notifications\Services;

use App\DataTransferObjects\Responses\EmptyResponseDTO;
use App\DataTransferObjects\Responses\ErrorResponseDTO;
use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use App\Errors\ErrorsIds;
use App\Repositories\BillingPlanRepository;
use Notifications\DataTransferObjects\Models\EmailTemplateDTO;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\DataTransferObjects\NotifTypeTriggerConfig\NotifTypeTriggerConfigConfigEmailDTO;
use Notifications\DataTransferObjects\Responses\EmailPostmarkAppConfigResponseDTO;
use Notifications\DataTransferObjects\Responses\EmailPostmarkAppDomainVerificationResponseDTO;
use Notifications\DataTransferObjects\Responses\EmailPostmarkAppTriggersResponseDTO;
use Notifications\DataTransferObjects\Responses\GetEmailPostmarkAppTriggerResponseDTO;
use Notifications\DataTransferObjects\Responses\SetEmailPostmarkAppSenderResponseDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Repositories\NotificationConfigurationRepository;
use Notifications\Repositories\NotificationTriggersRepository;
use Shipments\Repositories\StoreRepository;
use ShopifyStore\DataTransferObjects\Polaris\Button\PolarisButtonDTO;
use ShopifyStore\Enums\Polaris\PolarisActionButtonIDEnum;
use ShopifyStore\Enums\Polaris\PolarisButtonStyleEnum;

class EmailPostmarkAppService
{
    /**
     * NotificationTypeTriggerDTO.
     * @return NotificationTypeTriggerDTO[]
     */
    public static function getDefaultTriggers(): array
    {
        $forecastedPossibleDelayTemplateDTO = EmailTemplateService::getDefaultForecastedPossibleDelayTemplate();

        return [
            new NotificationTypeTriggerDTO(
                null,
                null,
                null,
                NotificationChannelEnum::EMAIL_POSTMARKAPP(),
                NotificationTriggerEnum::FORECASTED_POSSIBLE_DELAY(),
                false,
                null,
                null,
                null,
                null,
                null,
                null,
                NotifTypeTriggerConfigConfigEmailDTO::create(
                    $forecastedPossibleDelayTemplateDTO->subject,
                    $forecastedPossibleDelayTemplateDTO->body
                )),
        ];
    }

    /**
     * NotificationTypeTriggerDTO.
     * @return EmailTemplateDTO[]
     */
    public static function getDefaultEmailTemplates(): array
    {
        return [
            EmailTemplateService::getDefaultForecastedPossibleDelayTemplate(),
            EmailTemplateService::getCheckpointEmailTemplate(),
        ];
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function getConfiguration(int $storeID): ResponseDTO
    {
        $storeRepo = new StoreRepository();
        $existFlag = $storeRepo->isExist($storeID);

        if (! $existFlag) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::NOT_VALID_STORE_ID, true);
        }

        $notificationChannelEnum = NotificationChannelEnum::EMAIL_POSTMARKAPP();
        $featureEnum = NotificationChannelEnum::toFeatureEnum($notificationChannelEnum);

        $billingPlanRepo = new BillingPlanRepository();
        $featureSupported = $billingPlanRepo->isFeatureInActiveBilling($storeID, $featureEnum);

        if (! $featureSupported) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_PAYMENT_REQUIRED_402(), ErrorsIds::FEATURE_NOT_AVAILABLE);
        }

        $notificationConfRepo = new NotificationConfigurationRepository();

        $config = $notificationConfRepo->getNotificationChannelWithAllTriggers($storeID, NotificationChannelEnum::EMAIL_POSTMARKAPP());

        if (! $config) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_NOT_FOUND_404(), ErrorsIds::MISSING_NOTIFICATION_CHANNEL_CONFIGURATION, true);
        }
        $uiButtons = self::getDefaultUiButtons();

        $response = new EmailPostmarkAppConfigResponseDTO($config, $uiButtons);

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function getDomainVerification(int $storeID): ResponseDTO
    {
        $response = new EmailPostmarkAppDomainVerificationResponseDTO();

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function setDomainVerification(int $storeID): ResponseDTO
    {
        $response = new EmailPostmarkAppDomainVerificationResponseDTO();

        $response->translate();

        return $response;
    }

    /**
     * @param int $id
     * @param string $fromEmail
     * @param string $fromName
     * @return ResponseDTO
     */
    public static function setSender(int $id, string $fromEmail, string $fromName): ResponseDTO
    {
        $response = new SetEmailPostmarkAppSenderResponseDTO(
            $id,
            $fromEmail,
            $fromName
        );

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function deleteSender(int $storeID): ResponseDTO
    {
        $response = new SetEmailPostmarkAppSenderResponseDTO(
          null,
          null,
          null
        );

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     */
    public static function Triggers(int $storeID): ResponseDTO
    {
        $response = new EmailPostmarkAppTriggersResponseDTO();

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @return ResponseDTO
     * @throws \Throwable
     */
    public static function getTriggersMetaInformation(int $storeID): ResponseDTO
    {
        $triggers = NotificationTriggersRepository::getAllTriggers();

        $shortCodes = ShortCodeService::getShortCodes();

        $templates = self::getDefaultEmailTemplates();

        $statuses = ShipmentStatusService::getStatuses();

        $response = new EmailPostmarkAppTriggersResponseDTO($triggers, $statuses, $shortCodes, $templates);

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @param int $triggerID
     * @return ResponseDTO
     */
    public static function getTrigger(int $storeID, int $triggerID): ResponseDTO
    {
        $response = new GetEmailPostmarkAppTriggerResponseDTO();

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @param int $triggerID
     * @return ResponseDTO
     */
    public static function setTrigger(int $storeID, int $triggerID): ResponseDTO
    {
        $response = new GetEmailPostmarkAppTriggerResponseDTO();

        $response->translate();

        return $response;
    }

    /**
     * @param int $storeID
     * @param int $triggerID
     * @return ResponseDTO
     */
    public static function deleteTrigger(int $storeID, int $triggerID): ResponseDTO
    {
        return new EmptyResponseDTO();
    }

    /**
     * @return PolarisButtonDTO[]
     */
    public static function getDefaultUiButtons(): array
    {
        return [
            PolarisButtonDTO::make(
                PolarisActionButtonIDEnum::UPGRADE_TO_PLAN(),
                null,
                PolarisButtonStyleEnum::DEFAULT(),
                'stm.charge_limit_reached.action',
                true,
                'https://apps.shopify.com/no-contact-delivery#reviews',
                'POST:https://api.tryrush.com/shopify/v1/store/121212/tasks/is_review_written/complete',
                ),
            PolarisButtonDTO::make(
                PolarisActionButtonIDEnum::UPGRADE_TO_PLAN(),
                null,
                PolarisButtonStyleEnum::DEFAULT(),
                'stm.charge_limit_reached.action',
                true,
                'https://apps.shopify.com/no-contact-delivery#reviews',
                'POST:https://api.tryrush.com/shopify/v1/store/121212/tasks/is_review_written/complete',
                ),
        ];
    }
}

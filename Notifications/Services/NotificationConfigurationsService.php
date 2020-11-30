<?php

namespace Notifications\Services;

use App\DataTransferObjects\Responses\EmptyResponseDTO;
use App\DataTransferObjects\Responses\ErrorResponseDTO;
use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use App\Errors\ErrorsIds;
use Data\Enums\FeatureEnum;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Repositories\NotificationConfigurationRepository;

class NotificationConfigurationsService
{
    /**
     * @param int $storeId
     * @param int $featureID
     * @param bool $flag
     * @return ResponseDTO
     */
    public static function setActiveStatus(int $storeId, int $featureID, bool $flag): ResponseDTO
    {
        $featureEnum = FeatureEnum::make($featureID);

        $notificationChannelEnum = NotificationChannelEnum::fromFeatureEnum($featureEnum);
        if (! $notificationChannelEnum) {
            return ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_BAD_REQUEST_400(), ErrorsIds::FEATURE_DOES_NOT_SUPPORT_ACTIVE_STATUS);
        }

        $notificationConfigurationRepo = new NotificationConfigurationRepository();
        $config = $notificationConfigurationRepo->findByChannel($storeId, $notificationChannelEnum);

        $response = null;

        if (is_null($config)) {
            // case where we do not fire billing events, we are requesting activation of a feature that does not have record
            // for this cases this if is added to create a row
            $notificationConfigurationRepo->getOrCreate($storeId, $notificationChannelEnum, $flag);
        } else {
            if ($config->isActive && $flag) {
                $response = ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_CONFLICT_409(), ErrorsIds::FEATURE_ALREADY_ACTIVATED);
            } elseif (! $config->isActive && ! $flag) {
                $response = ErrorResponseDTO::fromTemplate(HTTPStatusCodeEnum::ERROR_CONFLICT_409(), ErrorsIds::FEATURE_ALREADY_DEACTIVATED);
            }

            if (is_null($response)) {
                $notificationConfigurationRepo->updateActiveFlag($config, $flag);
            }
        }

        // It is good to check if triggers for this features are already create just in case if they are not (old issues);
        $notifConfigDTO = $notificationConfigurationRepo->getNotificationChannelWithAllTriggers($storeId, $notificationChannelEnum);
        // we should be getting only
        if (is_null($notifConfigDTO->typeTriggers)) {
            // OK, we need
            if (NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT()->equals($notifConfigDTO->notificationChannel)) {
                ShopifyFulfilmentService::createDefaultTriggers($storeId, $flag);
            } elseif (NotificationChannelEnum::KLAVIYO()->equals($notifConfigDTO->notificationChannel)) {
                KlaviyoService::createDefaultTriggers($storeId, $notifConfigDTO->id, $flag);
            }
        }

        if (is_null($response)) {
            $response = new EmptyResponseDTO();
        }

        return $response;
    }

    /**
     * Check if notification channel is active per feature ID. Returns null if channel is not found.
     * @param int $storeId
     * @param FeatureEnum $feature
     * @return bool|null
     */
    public static function getActiveStatus(int $storeId, FeatureEnum $feature): ?bool
    {
        $notificationConfigurationRepo = new NotificationConfigurationRepository();

        $notificationConfigurationDTO = $notificationConfigurationRepo->findByFeature($storeId, $feature);
        if (is_null($notificationConfigurationDTO)) {
            return null;
        }

        return $notificationConfigurationDTO->isActive;
    }
}

<?php

declare(strict_types=1);

namespace Notifications\Services;

use Data\DataTransferObjects\Translations\TranslationDTO;
use Notifications\DataTransferObjects\Models\EmailTemplateDTO;

class EmailTemplateService
{
    public static function getCheckpointEmailTemplate(): EmailTemplateDTO
    {
        $slug = 'new_checkpoint';
        $emailContent = resource_path('views/emails/shipmentNotifications/new-checkpoint.blade.php');

        return self::_getTemplateDTO($slug, $emailContent);
    }

    public static function getDefaultForecastedPossibleDelayTemplate(): EmailTemplateDTO
    {
        $slug = 'forecasted_possible_delay';
        $emailContent = resource_path('views/emails/shipmentNotifications/new-shipment-status-delivered.blade.php');

        return self::_getTemplateDTO($slug, $emailContent);
    }

    private static function _getTemplateDTO(string $slug, string $templateLocation): EmailTemplateDTO
    {
        return new EmailTemplateDTO(
            $slug,
            new TranslationDTO('emails.template.'.$slug),
            'https://assets.tryrush.io/shopifyapp/emails/'.$slug.'.png',
            new TranslationDTO('emails.template.'.$slug),
            file_get_contents($templateLocation));
    }
}

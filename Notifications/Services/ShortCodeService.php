<?php

declare(strict_types=1);

namespace Notifications\Services;

use Data\DataTransferObjects\Translations\TranslationDTO;
use Notifications\DataTransferObjects\Meta\ShortCodesDTO;

class ShortCodeService
{
    private static function shortCodes(): array
    {
        return [
            [
                'slug' => '{store_name}',
                'label' => 'short_codes.store_name.label',
            ],
            [
                'slug' => '{store_url}',
                'label' => 'short_codes.store_url.label',
            ],
        ];
    }

    public static function getShortCodes(): array
    {
        $shortCodes = [];
        foreach (self::shortCodes() as $shortCode) {
            $shortCodes[] = new ShortCodesDTO(
              $shortCode['slug'],
              new TranslationDTO($shortCode['label'])
            );
        }

        return $shortCodes;
    }
}

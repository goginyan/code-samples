<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Data\Enums\LanguageEnum;
use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigKlaviyoDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    private static string $_JSON_VAR_LANGUAGE = 'language';
    private static string $_JSON_VAR_LANGUAGE_DEFAULT_VALUE = 'en';

    private static string $_JSON_VAR_PUBLIC_API_KEY = 'public_api_key';
    private static ?string $_JSON_VAR_PUBLIC_API_KEY_DEFAULT_VALUE = null;

    public ?LanguageEnum $language;

    public ?string $publicApiKey;

    public function __construct(array $parameters = [])
    {
        $languageId = self::getValue(
            $parameters,
            self::$_JSON_VAR_LANGUAGE,
            LanguageEnum::EN()->value);

        $this->language = LanguageEnum::make($languageId);

        $this->publicApiKey = self::getValue(
            $parameters,
            self::$_JSON_VAR_PUBLIC_API_KEY,
            self::$_JSON_VAR_PUBLIC_API_KEY_DEFAULT_VALUE);

        parent::__construct(NotificationChannelEnum::KLAVIYO());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_LANGUAGE => $this->language->value,
            self::$_JSON_VAR_PUBLIC_API_KEY => $this->publicApiKey,
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_LANGUAGE => self::$_JSON_VAR_LANGUAGE_DEFAULT_VALUE,
            self::$_JSON_VAR_PUBLIC_API_KEY => self::$_JSON_VAR_PUBLIC_API_KEY_DEFAULT_VALUE,
        ]);
    }
}

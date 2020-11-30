<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Data\Enums\LanguageEnum;
use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigSlackDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    private static string $_JSON_VAR_WEBHOOK_URL = 'webhook_url';
    private static ?string $_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_LANGUAGE = 'language';

    public ?string $webhookURL;

    public ?LanguageEnum $language;

    public function __construct(array $parameters = [])
    {
        $this->webhookURL = self::getValue(
            $parameters,
            self::$_JSON_VAR_WEBHOOK_URL,
            self::$_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE);

        $langaugeId = self::getValue(
            $parameters,
            self::$_JSON_VAR_LANGUAGE,
            LanguageEnum::EN()->value);

        $this->language = LanguageEnum::make($langaugeId);

        parent::__construct(NotificationChannelEnum::SLACK());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_LANGUAGE => $this->language->value,
            self::$_JSON_VAR_WEBHOOK_URL => $this->webhookURL,
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_LANGUAGE => LanguageEnum::EN()->value,
            self::$_JSON_VAR_WEBHOOK_URL => self::$_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE,
        ]);
    }
}

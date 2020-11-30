<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigCustomWebhookDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    private static string $_JSON_VAR_WEBHOOK_URL = 'webhook_url';
    private static ?string $_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_WEBHOOK_SECRET = 'webhook_secret';
    private static ?string $_JSON_VAR_WEBHOOK_SECRET_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_LANGUAGE = 'language';
    private static string $_JSON_VAR_LANGUAGE_DEFAULT_VALUE = 'en';

    public ?string $webhookUrl;

    public ?string $webhookSecret;

    public string $language;

    public function __construct(array $parameters = [])
    {
        $this->webhookUrl = self::getValue(
            $parameters,
            self::$_JSON_VAR_WEBHOOK_URL,
            self::$_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE);
        $this->webhookSecret = self::getValue(
            $parameters,
            self::$_JSON_VAR_WEBHOOK_SECRET,
            self::$_JSON_VAR_WEBHOOK_SECRET_DEFAULT_VALUE);
        $this->language = self::getValue(
            $parameters,
            self::$_JSON_VAR_LANGUAGE,
            self::$_JSON_VAR_LANGUAGE_DEFAULT_VALUE);

        parent::__construct(NotificationChannelEnum::CUSTOM_WEBHOOKS());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_WEBHOOK_URL => $this->webhookUrl,
            self::$_JSON_VAR_WEBHOOK_SECRET => $this->webhookSecret,
            self::$_JSON_VAR_LANGUAGE => $this->language,
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_WEBHOOK_URL => self::$_JSON_VAR_WEBHOOK_URL_DEFAULT_VALUE,
            self::$_JSON_VAR_WEBHOOK_SECRET => self::$_JSON_VAR_WEBHOOK_SECRET_DEFAULT_VALUE,
            self::$_JSON_VAR_LANGUAGE => self::$_JSON_VAR_LANGUAGE_DEFAULT_VALUE,
        ]);
    }
}

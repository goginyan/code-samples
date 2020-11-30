<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigPayPalDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    private static string $_JSON_VAR_API_SECRET = 'api_secret';
    private static ?string $_JSON_VAR_API_SECRET_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_ACCOUNT_EMAIL = 'account_email';
    private static ?string $_JSON_VAR_ACCOUNT_EMAIL_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_ACCOUNT_ID = 'account_id';
    private static ?string $_JSON_VAR_ACCOUNT_ID_DEFAULT_VALUE = null;

    public ?string $apiSecret;

    public ?string $accountEmail;

    public ?string $accountId;

    public function __construct(array $parameters = [])
    {
        $this->apiSecret = self::getValue(
            $parameters,
            self::$_JSON_VAR_API_SECRET,
            self::$_JSON_VAR_API_SECRET_DEFAULT_VALUE);
        $this->accountEmail = self::getValue(
            $parameters,
            self::$_JSON_VAR_ACCOUNT_EMAIL,
            self::$_JSON_VAR_ACCOUNT_EMAIL_DEFAULT_VALUE);
        $this->accountId = self::getValue(
            $parameters,
            self::$_JSON_VAR_ACCOUNT_ID,
            self::$_JSON_VAR_ACCOUNT_ID_DEFAULT_VALUE);

        parent::__construct(NotificationChannelEnum::PAYPAL());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_API_SECRET => $this->apiSecret,
            self::$_JSON_VAR_ACCOUNT_EMAIL => $this->accountEmail,
            self::$_JSON_VAR_ACCOUNT_ID => $this->accountId,
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_API_SECRET => self::$_JSON_VAR_API_SECRET_DEFAULT_VALUE,
            self::$_JSON_VAR_ACCOUNT_EMAIL => self::$_JSON_VAR_ACCOUNT_EMAIL_DEFAULT_VALUE,
            self::$_JSON_VAR_ACCOUNT_ID => self::$_JSON_VAR_ACCOUNT_ID_DEFAULT_VALUE,
        ]);
    }
}

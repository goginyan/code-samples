<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigEmailPostmarkAppDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    private static string $_JSON_VAR_SENDER_EMAIL = 'sender_email';
    private static ?string $_JSON_VAR_SENDER_EMAIL_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_SENDER_NAME = 'sender_name';
    private static ?string $_JSON_VAR_SENDER_NAME_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_DKIM_VERIFIED = 'dkim_verified';
    private static ?string $_JSON_VAR_DKIM_VERIFIED_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_CNAME_1_VERIFIED = 'cname1_verified';
    private static ?string $_JSON_VAR_CNAME_1_VERIFIED_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_CNAME_2_VERIFIED = 'cname2_verified';
    private static ?string $_JSON_VAR_CNAME_2_VERIFIED_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_CNAME_3_VERIFIED = 'cname3_verified';
    private static ?string $_JSON_VAR_CNAME_3_VERIFIED_DEFAULT_VALUE = null;

    private static string $_JSON_VAR_CNAME_VERIFIED_AT = 'cname_verified_at';
    private static ?string $_JSON_VAR_CNAME_VERIFIED_AT_DEFAULT_VALUE = null;

    public ?string $senderEmail;
    public ?string $senderName;
    public ?bool $dkimVerified;
    public ?string $cname1Verified;
    public ?string $cname2Verified;
    public ?string $cname3Verified;
    public ?\DateTime $cnameVerifiedAt;

    public function __construct(array $parameters = [])
    {
        $this->senderEmail = self::getValue(
            $parameters,
            self::$_JSON_VAR_SENDER_EMAIL,
            self::$_JSON_VAR_SENDER_EMAIL_DEFAULT_VALUE);
        $this->senderName = self::getValue(
            $parameters,
            self::$_JSON_VAR_SENDER_NAME,
            self::$_JSON_VAR_SENDER_NAME_DEFAULT_VALUE);
        $this->dkimVerified = self::getValue(
            $parameters,
            self::$_JSON_VAR_DKIM_VERIFIED,
            self::$_JSON_VAR_DKIM_VERIFIED_DEFAULT_VALUE);
        $this->cname1Verified = self::getValue(
            $parameters,
            self::$_JSON_VAR_CNAME_1_VERIFIED,
            self::$_JSON_VAR_CNAME_1_VERIFIED_DEFAULT_VALUE);
        $this->cname2Verified = self::getValue(
            $parameters,
            self::$_JSON_VAR_CNAME_2_VERIFIED,
            self::$_JSON_VAR_CNAME_2_VERIFIED_DEFAULT_VALUE);
        $this->cname3Verified = self::getValue(
            $parameters,
            self::$_JSON_VAR_CNAME_3_VERIFIED,
            self::$_JSON_VAR_CNAME_3_VERIFIED_DEFAULT_VALUE);
        $this->cnameVerifiedAt = self::fromStringToFromDateTime($parameters[self::$_JSON_VAR_CNAME_VERIFIED_AT]);

        parent::__construct(NotificationChannelEnum::EMAIL_POSTMARKAPP());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_SENDER_EMAIL => $this->senderEmail,
            self::$_JSON_VAR_SENDER_NAME => $this->senderName,
            self::$_JSON_VAR_DKIM_VERIFIED => $this->dkimVerified,
            self::$_JSON_VAR_CNAME_1_VERIFIED => $this->cname1Verified,
            self::$_JSON_VAR_CNAME_2_VERIFIED => $this->cname2Verified,
            self::$_JSON_VAR_CNAME_3_VERIFIED => $this->cname3Verified,
            self::$_JSON_VAR_CNAME_VERIFIED_AT => self::fromFromDateTimeToString($this->cnameVerifiedAt),
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_SENDER_EMAIL => self::$_JSON_VAR_SENDER_EMAIL_DEFAULT_VALUE,
            self::$_JSON_VAR_SENDER_NAME => self::$_JSON_VAR_SENDER_NAME_DEFAULT_VALUE,
            self::$_JSON_VAR_DKIM_VERIFIED => self::$_JSON_VAR_DKIM_VERIFIED_DEFAULT_VALUE,
            self::$_JSON_VAR_CNAME_1_VERIFIED => self::$_JSON_VAR_CNAME_1_VERIFIED_DEFAULT_VALUE,
            self::$_JSON_VAR_CNAME_2_VERIFIED => self::$_JSON_VAR_CNAME_2_VERIFIED_DEFAULT_VALUE,
            self::$_JSON_VAR_CNAME_3_VERIFIED => self::$_JSON_VAR_CNAME_3_VERIFIED_DEFAULT_VALUE,
            self::$_JSON_VAR_CNAME_VERIFIED_AT => self::$_JSON_VAR_CNAME_VERIFIED_AT_DEFAULT_VALUE,
        ]);
    }
}

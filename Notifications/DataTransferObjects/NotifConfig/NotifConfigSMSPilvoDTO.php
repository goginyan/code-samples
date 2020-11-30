<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigSMSPilvoDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    public function __construct(array $parameters = [])
    {
        parent::__construct(NotificationChannelEnum::SMS_PLIVO());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([]);
    }

    public static function getDefault(): self
    {
        return new self([]);
    }
}

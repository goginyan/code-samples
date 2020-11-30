<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigFBMessengerDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    public function __construct(array $parameters = [])
    {
        parent::__construct(NotificationChannelEnum::FB_MESSENGER_BOT());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return static
     */
    public static function getDefault(): self
    {
        return new self([]);
    }
}

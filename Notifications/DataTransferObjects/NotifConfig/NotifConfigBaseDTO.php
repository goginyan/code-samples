<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\Enums\NotificationChannelEnum;
use Spatie\DataTransferObject\DataTransferObject;

abstract class NotifConfigBaseDTO extends DataTransferObject
{
    private NotificationChannelEnum $_channel;

    public function __construct(NotificationChannelEnum $channel)
    {
        $this->_channel = $channel;
    }

    public function getChannel(): NotificationChannelEnum
    {
        return $this->_channel;
    }

    protected static function getValue(array $params, string $field, $defaultValue)
    {
        if (isset($params[$field])) {
            return $params[$field];
        }

        return $defaultValue;
    }

    protected static function fromFromDateTimeToString(?\DateTime $value): ?string
    {
        return $value ? $value->format('Y-m-d H:i:s') : null;
    }

    protected static function fromStringToFromDateTime(?string $value): ?\DateTime
    {
        if (empty($value)) {
            return null;
        }

        return $value ? new \DateTime($value) : null;
    }

    abstract public function toDatabaseJSON(): string;
}

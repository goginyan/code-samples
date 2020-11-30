<?php

namespace Notifications\DataTransferObjects\NotifTypeTriggerConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Spatie\DataTransferObject\DataTransferObject;

abstract class NotifTypeTriggerConfigBaseDTO extends DataTransferObject implements DatabaseJsonInterface
{
    abstract public static function fromDatabaseJSON(array $JSONData): self;

    abstract public function toDatabaseJSON(): string;
}

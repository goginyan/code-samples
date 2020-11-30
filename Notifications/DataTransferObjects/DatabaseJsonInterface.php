<?php

namespace Notifications\DataTransferObjects;

interface DatabaseJsonInterface
{
    /**
     * @param array $JSONData
     * @return static
     */
    public static function fromDatabaseJSON(array $JSONData): self;

    /**
     * @return string
     */
    public function toDatabaseJSON(): string;
}

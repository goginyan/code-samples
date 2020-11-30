<?php

namespace Notifications\DataTransferObjects\NotifTypeTriggerConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;

class NotifTypeTriggerConfigConfigSMSDTO extends NotifTypeTriggerConfigBaseDTO implements DatabaseJsonInterface
{
    public string $message;

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self([
            'message' => $JSONData['message'],
        ]);
    }

    /**
     * @return string
     */
    public function toDatabaseJSON(): string
    {
        return json_encode([
            'message' => $this->message,
        ], JSON_UNESCAPED_SLASHES);
    }
}

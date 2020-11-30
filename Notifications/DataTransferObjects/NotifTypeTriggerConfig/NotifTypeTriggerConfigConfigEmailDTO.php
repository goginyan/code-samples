<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\NotifTypeTriggerConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;

class NotifTypeTriggerConfigConfigEmailDTO extends NotifTypeTriggerConfigBaseDTO implements DatabaseJsonInterface
{
    public string $subject;

    public string $body;

    public static function create(string $subject, string $body): self
    {
        return new self([
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self([
            'subject' => $JSONData['subject'],
            'body' => $JSONData['body'],
        ]);
    }

    /**
     * @return string
     */
    public function toDatabaseJSON(): string
    {
        return json_encode([
            'subject' => $this->subject,
            'body' => $this->body,
        ], JSON_UNESCAPED_SLASHES);
    }
}

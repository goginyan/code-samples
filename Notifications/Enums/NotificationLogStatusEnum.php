<?php

namespace Notifications\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self SUCCESS()
 * @method static self FAILED()
 */
final class NotificationLogStatusEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'FAILED' => 0,
            'SUCCESS' => 1,
        ];
    }
}

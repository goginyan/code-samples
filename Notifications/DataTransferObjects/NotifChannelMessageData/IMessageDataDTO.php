<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\NotifChannelMessageData;

use Illuminate\Contracts\Support\Arrayable;
use Notifications\Enums\NotificationChannelEnum;

interface IMessageDataDTO extends Arrayable
{
    public function getChannel(): NotificationChannelEnum;
}

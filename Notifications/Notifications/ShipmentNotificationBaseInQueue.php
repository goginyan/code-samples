<?php

declare(strict_types=1);

namespace Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShipmentNotificationBaseInQueue extends ShipmentNotificationBase implements ShouldQueue
{
    use Queueable;
}

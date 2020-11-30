<?php

namespace Notifications\Listeners;

use Notifications\Services\NotificationManager;
use Shipments\Events\ShipmentCheckpointBaseEvent;

class NotificationsShipmentCheckpointBaseEventListener
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(ShipmentCheckpointBaseEvent $event)
    {
        NotificationManager::sendShipment($event->trigger, $event->storeID, $event->shipmentID, $event->checkpointID, false);
    }
}

<?php

namespace Notifications\Listeners;

use Notifications\Services\NotificationManager;
use Shipments\Events\ShipmentBaseEvent;

class NotificationsShipmentBaseEventListener
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(ShipmentBaseEvent $event)
    {
        NotificationManager::sendShipment($event->trigger, $event->storeID, $event->shipmentID, null, false);
    }
}

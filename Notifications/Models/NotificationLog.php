<?php

namespace Notifications\Models;

use App\Models\Shipment;
use App\Models\ShipmentsHistoryInfo;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notificationTypeTrigger()
    {
        return $this->belongsTo(NotificationTypeTrigger::class, 'notification_type_trigger_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notificationChannel()
    {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo#
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newestShipmentHistoryInfo()
    {
        return $this->belongsTo(ShipmentsHistoryInfo::class, 'newest_shipments_history_info_id');
    }
}

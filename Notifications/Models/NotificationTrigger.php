<?php

namespace Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTrigger extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationTypeTriggers()
    {
        return $this->hasMany(NotificationTypeTrigger::class, 'trigger_id');
    }
}

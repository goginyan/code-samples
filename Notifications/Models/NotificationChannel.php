<?php

namespace Notifications\Models;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}

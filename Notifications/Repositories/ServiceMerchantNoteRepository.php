<?php

namespace Notifications\Repositories;

use App\Models\ServiceMerchantNote;
use App\Models\Store;
use Data\Enums\FeatureEnum;

class ServiceMerchantNoteRepository
{
    /**
     * @param Store $store
     * @param FeatureEnum[] $features
     */
    public static function createDefaultByStore(Store $store, array $features): void
    {
        foreach ($features as $feature) {
            $data = [
                'store_id' => $store->id,
                'is_active' => false,
                'type' => 'floating',
                'feature_id' => $feature->value,
                'title' => 'Merchant’s Note',
                'message' => 'Shipments may be delayed due holiday season',
                'bkg_color_style' => 'dark',
                'text_color_style' => 'white',
                'is_dismissible' => false,
                'icon_style' => 'info',
            ];

            ServiceMerchantNote::query()->create($data);
        }
    }
}

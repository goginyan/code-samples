<?php

namespace Notifications\DataTransferObjects\Models;

use Data\DataTransferObjects\Translations\TranslationDTO;
use Data\Enums\FeatureEnum;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Models\NotificationChannel;
use Spatie\DataTransferObject\DataTransferObject;

class NotificationChannelDTO extends DataTransferObject
{
    public int $id;
    public NotificationChannelEnum $slug;
    public TranslationDTO $label;
    public TranslationDTO $description;
    public FeatureEnum $feature;

    /**
     * @param NotificationChannel $model
     * @return static
     */
    public static function fromModel(NotificationChannel $model): self
    {
        return new self([
            'id' => $model->id,
            'slug' => NotificationChannelEnum::make($model->slug),
            'label' => new TranslationDTO($model->label_translation_loc_id, null),
            'description' => new TranslationDTO($model->description_translation_loc_id, null),
            'feature' => FeatureEnum::make($model->feature_id),
        ]);
    }
}

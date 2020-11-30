<?php

namespace Notifications\DataTransferObjects\Models;

use Data\DataTransferObjects\Features\FeatureDTO;
use Illuminate\Contracts\Support\Arrayable;
use ShopifyStore\Enums\ServiceUpsellTypeEnum;
use Spatie\DataTransferObject\DataTransferObject;
use Store\Models\ServiceUpsell;

class ServiceUpsellDTO extends DataTransferObject implements Arrayable
{
    public ?int $id;
    public bool $isActive;
    public ?ServiceUpsellTypeEnum $type;
    public ?FeatureDTO $feature;
    public ?int $collectionId;
    public ?string $title;

    /**
     * @param ServiceUpsell $model
     * @return ServiceUpsellDTO
     */
    public static function fromModel(ServiceUpsell $model): self
    {
        $type = null;
        if (! empty($model->type) && ServiceUpsellTypeEnum::exist($model->type)) {
            $type = ServiceUpsellTypeEnum::make($model->type);
        }

        return new self([
            'id' => $model->id,
            'isActive' => $model->is_active,
            'type' => $type,
            'feature' => $model->feature ? FeatureDTO::fromDBModel($model->feature) : null,
            'collectionId' => $model->collection_id,
            'title' => $model->title,
        ]);
    }

    /**
     * @param array $data
     * @return ServiceUpsellDTO
     */
    public static function fromArray(array $data): self
    {
        $type = null;
        if (! empty($data['type']) && ServiceUpsellTypeEnum::exist($data['type'])) {
            $type = ServiceUpsellTypeEnum::make($data['type']);
        }

        return new self([
            'isActive' => $data['is_active'],
            'type' => $type,
            'collectionId' => $data['collection_id'],
            'title' => $data['title'],
        ]);
    }

    /**
     * @return array
     */
    public function toEloquentUpdate(): array
    {
        return [
            'is_active' => $this->isActive,
            'type' => ($this->type) ? $this->type->value : null,
            'collection_id' => $this->collectionId,
            'title' => $this->title,
        ];
    }
}

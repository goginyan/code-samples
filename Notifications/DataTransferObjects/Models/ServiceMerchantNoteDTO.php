<?php

namespace Notifications\DataTransferObjects\Models;

use App\Models\ServiceMerchantNote;
use Data\DataTransferObjects\Features\FeatureDTO;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;

class ServiceMerchantNoteDTO extends DataTransferObject implements Arrayable
{
    public bool $isActive;
    public ?string $type;
    public ?FeatureDTO $feature;
    public ?string $title;
    public ?string $message;
    public ?string $bkgColorStyle;
    public ?string $textColorStyle;
    public ?bool $isDismissible;
    public ?string $iconStyle;

    /**
     * @param ServiceMerchantNote $model
     * @return $this
     */
    public static function fromModel(ServiceMerchantNote $model): self
    {
        return new self([
            'isActive' => $model->is_active,
            'type' => $model->type,
            'feature' => $model->feature ? FeatureDTO::fromDBModel($model->feature) : null,
            'title' => $model->title,
            'message' => $model->message,
            'bkgColorStyle' => $model->bkg_color_style,
            'textColorStyle' => $model->text_color_style,
            'isDismissible' => $model->is_dismissible,
            'iconStyle' => $model->icon_style,
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'is_active' => $this->isActive,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'bkg_color_style' => $this->bkgColorStyle,
            'text_color_style' => $this->textColorStyle,
            'dismissible' => $this->isDismissible,
            'icon_style' => $this->iconStyle,
        ];
    }
}

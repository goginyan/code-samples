<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\Meta;

use App\Support\Helpers\ArrExtended;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;

class ShortCodesDTO implements Arrayable
{
    public ?string $slug;
    public ?TranslationDTO $label;

    public function __construct(
        ?string $slug,
        ?TranslationDTO $label
    ) {
        $this->slug = $slug;
        $this->label = $label;
    }

    public function toArray(): array
    {
        return ArrExtended::filter_nulls([
            'slug' => $this->slug,
            'label' => $this->label->getText(),
        ]);
    }
}

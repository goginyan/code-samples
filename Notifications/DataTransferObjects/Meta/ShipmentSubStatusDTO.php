<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\Meta;

use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;

class ShipmentSubStatusDTO implements Arrayable
{
    use Translatable;

    public string $id;
    public TranslationDTO $label;

    public function __construct(string $id, TranslationDTO $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'label' => $this->label->getText(),
        ];
    }
}

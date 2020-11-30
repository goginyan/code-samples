<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\Meta;

use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;
use ShopifyStore\DataTransferObjects\Polaris\PolarisBadgeDTO;

class ShipmentStatusDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public string $id;
    public TranslationDTO $label;
    public bool $isFinal;
    public PolarisBadgeDTO $badgeProp;
    /**
     * @var ShipmentSubStatusDTO[]
     */
    public array $subStatuses;

    public function __construct(
        string $id,
        TranslationDTO $label,
        bool $isFinal,
        PolarisBadgeDTO $badgeProp,
        array $subStatuses
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->isFinal = $isFinal;
        $this->badgeProp = $badgeProp;
        $this->subStatuses = $subStatuses;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'label' => $this->label->getText(),
            'is_final' => $this->isFinal,
            'substatuses' => $this->subStatuses ? $this->toArrayElements($this->subStatuses) : null,
            'badge_prop' => $this->badgeProp->toArray(),
        ];
    }
}

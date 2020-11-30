<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\Models;

use App\Support\Helpers\ArrExtended;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;

class NotificationTriggerDTO implements Arrayable
{
    public ?string $slug;
    public ?TranslationDTO $label;
    public ?bool $supportShipmentStatuses;
    public ?bool $supportNoChangeLimit;

    public function __construct(
        ?string $slug,
        ?TranslationDTO $label,
        bool $supportShipmentStatuses,
        bool $supportNoChangeLimit
    ) {
        $this->slug = $slug;
        $this->label = $label;
        $this->supportShipmentStatuses = $supportShipmentStatuses;
        $this->supportNoChangeLimit = $supportNoChangeLimit;
    }

    public function toArray(): array
    {
        return ArrExtended::filter_nulls([
            'slug' => $this->slug,
            'label' => $this->label->getText(),
            'support_shipment_statuses' => $this->supportShipmentStatuses,
            'support_no_change_limit' => $this->supportNoChangeLimit,
        ]);
    }
}

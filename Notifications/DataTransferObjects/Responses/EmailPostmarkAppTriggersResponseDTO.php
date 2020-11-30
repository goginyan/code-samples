<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Notifications\DataTransferObjects\Meta\ShipmentStatusDTO;
use Notifications\DataTransferObjects\Meta\ShortCodesDTO;
use Notifications\DataTransferObjects\Models\EmailTemplateDTO;
use Notifications\DataTransferObjects\Models\NotificationTriggerDTO;

class EmailPostmarkAppTriggersResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    /**
     * @var NotificationTriggerDTO[]|null
     */
    public ?array $triggers;
    /**
     * @var ShipmentStatusDTO[]|null
     */
    public ?array $statuses;
    /**
     * @var array|ShortCodesDTO[]|null
     */
    public ?array $shortCodes;

    /**
     * @var array|EmailTemplateDTO[]|null
     */
    public ?array $templates;

    /**
     * EmailPostmarkAppTriggersResponseDTO constructor.
     * @param NotificationTriggerDTO[]|null $triggers
     * @param ShipmentStatusDTO[]|null $statuses
     * @param ShortCodesDTO[]|null $shortCodes
     * @param EmailTemplateDTO[]|null $templates
     */
    public function __construct(?array $triggers, ?array $statuses, ?array $shortCodes, ?array $templates)
    {
        $this->triggers = $triggers;
        $this->statuses = $statuses;
        $this->shortCodes = $shortCodes;
        $this->templates = $templates;
        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();

        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'meta' => [
                    'triggers' => $this->triggers ? $this->toArrayElements($this->triggers) : null,
                    'statuses' => $this->toArrayElements($this->statuses),
                    'shortcodes' => $this->toArrayElements($this->shortCodes),
                    'templates' => $this->toArrayElements($this->templates),
                ],
            ],
        ];
    }
}

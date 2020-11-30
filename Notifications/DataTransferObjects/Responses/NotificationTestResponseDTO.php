<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;

class NotificationTestResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public TranslationDTO $status;

    public function __construct(
        TranslationDTO $status
    ) {
        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'message' => $this->status->getText(),
            ],
        ];
    }
}

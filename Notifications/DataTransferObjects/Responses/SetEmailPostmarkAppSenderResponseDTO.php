<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;

class SetEmailPostmarkAppSenderResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public ?int $id;
    public ?string $fromEmail;
    public ?string $fromName;

    /**
     * SetEmailPostmarkAppSenderResponseDTO constructor.
     * @param int|null $id
     * @param string|null $fromEmail
     * @param string|null $fromName
     */
    public function __construct(
        ?int $id,
        ?string $fromEmail,
        ?string $fromName
    ) {
        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();

        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'config' => [
                    'id' => 31,
                    'sender_name' => 'Shipment Notifications',
                    'sender_email' => 'updates@tryrush.io',
                    'dkim_label' => 'DKIM Verified',
                    'dkim_props' => [
                        'progress' => 'incomplete',
                        'status' => 'info',
                    ],
                    'ui_buttons' => [
                        'reset_to_sender' => [
                            'id' => 'update_usage_charges_cap_limit',
                            'style' => 'update_usage_charges_cap_limit',
                            'content' => 'Increase cap amount',
                            'external' => true,
                            'url' => 'https://apps.shopify.com/no-contact-delivery#reviews',
                            'click_track' => 'POST:https://api.tryrush.com/shopify/v1/store/121212/tasks/is_review_written/complete',
                        ],
                        'verify_domain' => [
                            'id' => 'update_usage_charges_cap_limit',
                            'style' => 'update_usage_charges_cap_limit',
                            'content' => 'Increase cap amount',
                            'external' => true,
                            'url' => 'https://apps.shopify.com/no-contact-delivery#reviews',
                            'click_track' => 'POST:https://api.tryrush.com/shopify/v1/store/121212/tasks/is_review_written/complete',
                        ],
                    ],
                ],
            ],
        ];
    }
}

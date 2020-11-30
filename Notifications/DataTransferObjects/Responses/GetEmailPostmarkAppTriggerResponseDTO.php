<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;

class GetEmailPostmarkAppTriggerResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    /**
     * GetEmailPostmarkAppTriggerResponseDTO constructor.
     */
    public function __construct()
    {
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
                    'summary' => 'When **New Status** is **Out for delivery**',
                    'is_active' => 'false;',
                    'active_label' => 'Inactive',
                    'active_props' => [
                        'progress' => 'incomplete',
                        'status' => 'info',
                    ],
                    'trigger' => 'new_checkpoint',
                    'filter_duration_label' => '3h 30m',
                    'sent_with_delay_label' => '5d',
                    'filter_status' => [
                        'develiverd',
                    ],
                    'filter_substatus' => 'string',
                    'email_subject' => 'Your {{order_title}} is deliverd',
                    'email_body' => 'new_checkpoint',
                ],
                'meta' => [
                    'triggers' => [
                        [
                            'slug' => 'new_shipment',
                            'label' => 'New Shipment',
                            'support_shipment_statuses' => false,
                            'support_no_change_limit' => false,
                        ],
                    ],
                    'statuses' => [
                        [
                            'id' => 'delivered',
                            'label' => 'Delivered',
                            'is_final' => true,
                            'substatues' => [
                                [
                                    'id' => 'delivered|mailbox',
                                    'label' => 'Mailbox',
                                ],
                            ],
                            'badge_prop' => [
                                'progress' => 'partiallyComplete',
                                'status' => 'attention',
                            ],
                        ],
                    ],
                    'shortcodes' => [
                        [
                            'slug' => 'fr',
                            'label' => 'French',
                        ],
                    ],
                    'templates' => [
                        [
                            'slug' => 'shipment_update',
                            'label' => 'Shipment status updated tempalte',
                            'image' => 'https://www.....',
                            'email_subject' => 'Your {{order_title}} is deliverd',
                            'email_body' => 'new_checkpoint',
                        ],
                    ],
                ],
            ],
        ];
    }
}

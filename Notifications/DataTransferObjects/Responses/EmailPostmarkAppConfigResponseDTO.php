<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;
use Notifications\DataTransferObjects\Models\NotificationConfigurationDTO;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use ShopifyStore\DataTransferObjects\Polaris\Button\PolarisButtonDTO;
use ShopifyStore\DataTransferObjects\Polaris\PolarisBadgeDTO;
use ShopifyStore\Enums\Polaris\PolarisBadgeProgressEnum;
use ShopifyStore\Enums\Polaris\PolarisBadgeStatusEnum;

class EmailPostmarkAppConfigResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public NotificationConfigurationDTO $config;
    public PolarisBadgeDTO $dkimProps;
    public ?TranslationDTO $dkimLabel;
    public ?array $uiButtons;
    /**
     * @var NotificationTypeTriggerDTO[]|null
     */
    public ?array $triggers;

    /**
     * EmailPostmarkAppConfigResponseDTO constructor.
     * @param NotificationConfigurationDTO $config
     * @param PolarisButtonDTO[]|null $uiButtons
     */
    public function __construct(
        NotificationConfigurationDTO $config,
        ?array $uiButtons
    ) {
        $this->config = $config;
        $this->triggers = $config->typeTriggers;
        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();
        $this->uiButtons = $uiButtons;
        if ($config && $config->config->dkimVerified) {
            $this->dkimProps = PolarisBadgeDTO::make(PolarisBadgeStatusEnum::SUCCESS(), PolarisBadgeProgressEnum::COMPLETE());
            $this->dkimLabel = new TranslationDTO('email.dkim.label_verified');
        } else {
            $this->dkimProps = PolarisBadgeDTO::make(PolarisBadgeStatusEnum::WARNING(), PolarisBadgeProgressEnum::INCOMPLETE());
            $this->dkimLabel = new TranslationDTO('email.dkim.label.non-verified');
        }

        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'config' => [
                    'id' => $this->config->id,
                    'sender_name' => $this->config->config->senderName,
                    'sender_email' => $this->config->config->senderEmail,
                    'dkim_label' => $this->dkimLabel->getText(),
                    'dkim_props' => $this->dkimProps,
                    'ui_buttons' => $this->uiButtons ? $this->toArrayElements($this->uiButtons) : null,
                ],
                'triggers' => self::getFormattedTriggers(),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getFormattedTriggers(): array
    {
        $result = [];

        if ($this->triggers) {
            foreach ($this->triggers as $trigger) {
                if ($trigger->isActive) {
                    $triggerProps = PolarisBadgeDTO::make(PolarisBadgeStatusEnum::SUCCESS(), PolarisBadgeProgressEnum::COMPLETE());
                } else {
                    $triggerProps = PolarisBadgeDTO::make(PolarisBadgeStatusEnum::WARNING(), PolarisBadgeProgressEnum::INCOMPLETE());
                }

                $result[] = [
                    'id' => $trigger->id,
                    'summary' => $trigger->getSummary(),
                    'is_active' => $trigger->isActive,
                    'active_label' => $trigger->activeLabel->getText(),
                    'active_props' =>  $triggerProps,
                ];
            }
        }

        return $result;
    }
}

<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\SlugTranslationDTO;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Data\Enums\LanguageEnum;
use Data\Helper\TranslationHelper;
use Illuminate\Contracts\Support\Arrayable;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Services\SlackService;

class SlackConfigResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public ?int $configId;
    public ?string $configWebhookURL;
    public ?LanguageEnum $configLanguage;
    public ?NotificationTriggerEnum $configTrigger;

    /**
     * @var SlugTranslationDTO|null
     */
    public ?array $metaTriggers = null;
    /**
     * @var SlugTranslationDTO|null
     */
    public ?array $metaLanguage = null;

    /**
     * SettingsAPIResponseDTO constructor.
     * @param BulkItemResponseDTO[] $data
     */
    public function __construct(
        ?int $configId,
        ?string $configWebhookURL,
        ?LanguageEnum $configLanguage,
        ?NotificationTriggerEnum $configTrigger
    ) {
        $this->configId = $configId;
        $this->configWebhookURL = $configWebhookURL;
        $this->configLanguage = $configLanguage;
        $this->configTrigger = $configTrigger;

        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();

        $this->metaTriggers = [];
        foreach (SlackService::getPossibleTriggers() as $trigger) {
            $slug = NotificationTriggerEnum::toSlug($trigger);
            $this->metaTriggers[] = new SlugTranslationDTO(
                $slug,
                new TranslationDTO(TranslationHelper::getNotificationTrigger($slug), null)
            );
        }

        $this->metaLanguage = [];
        foreach (SlackService::getPossibleLanguages() as $language) {
            $slug = LanguageEnum::toSlug($language);
            $this->metaLanguage[] = new SlugTranslationDTO(
                $slug,
                new TranslationDTO(TranslationHelper::getLanguage($slug), null)
            );
        }
    }

    public function toArray(): array
    {
        $config = [
            'id' => $this->configId,
            'webhook_url' => $this->configWebhookURL,
            'language' => is_null($this->configLanguage) ? null : LanguageEnum::toSlug($this->configLanguage),
            'trigger' => is_null($this->configTrigger) ? null : NotificationTriggerEnum::toSlug($this->configTrigger),
        ];

        return [
            'data' => [
                'config' => array_filter($config),
                'meta' => [
                    'triggers' => $this->toArrayElements($this->metaTriggers),
                    'languages' => $this->toArrayElements($this->metaLanguage),
                ],
            ],
        ];
    }
}

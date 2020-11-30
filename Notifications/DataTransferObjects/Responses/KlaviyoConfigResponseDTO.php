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
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Services\KlaviyoService;

class KlaviyoConfigResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    public ?int $configId;
    public ?string $configPublicApiKey;
    public ?LanguageEnum $configLanguage;
    public ?array $configTrigger;

    /**
     * @var SlugTranslationDTO|null
     */
    public ?array $metaTriggers = null;
    /**
     * @var SlugTranslationDTO|null
     */
    public ?array $metaLanguage = null;

    /**
     * KlaviyoConfigResponseDTO constructor.
     * @param int|null $configId
     * @param string|null $configPublicApiKey
     * @param LanguageEnum|null $configLanguage
     * @param NotificationTypeTriggerDTO[]|null $notificationTypeTriggers
     */
    public function __construct(
        ?int $configId,
        ?string $configPublicApiKey,
        ?LanguageEnum $configLanguage,
        ?array $notificationTypeTriggers
    ) {
        $this->configId = $configId;
        $this->configPublicApiKey = $configPublicApiKey;
        $this->configLanguage = $configLanguage;
        $this->configTrigger = $notificationTypeTriggers;

        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();

        $this->metaTriggers = [];
        foreach (KlaviyoService::getPossibleTriggers() as $trigger) {
            $slug = NotificationTriggerEnum::toSlug($trigger);
            $this->metaTriggers[] = new SlugTranslationDTO(
                $slug,
                new TranslationDTO(TranslationHelper::getNotificationTrigger($slug), null)
            );
        }

        $this->metaLanguage = [];
        foreach (KlaviyoService::getPossibleLanguages() as $language) {
            $slug = LanguageEnum::toSlug($language);
            $this->metaLanguage[] = new SlugTranslationDTO(
                $slug,
                new TranslationDTO(TranslationHelper::getLanguage($slug), null)
            );
        }
    }

    public function toArray(): array
    {
        $triggers = [];
        if (! is_null($this->configTrigger)) {
            foreach ($this->configTrigger as $trigger) {
                $triggers[] = [
                    'id' => $trigger->id,
                    'trigger_slug' => NotificationTriggerEnum::toSlug($trigger->trigger),
                    'is_active' => $trigger->isActive,
                ];
            }
        }
        $config = [
            'id' => $this->configId,
            'public_api_key' => $this->configPublicApiKey,
            'language' => is_null($this->configLanguage) ? null : LanguageEnum::toSlug($this->configLanguage),
            'triggers' => $triggers,
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

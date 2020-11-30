<?php

declare(strict_types=1);

namespace Notifications\Repositories;

use Data\DataTransferObjects\Translations\TranslationDTO;
use Notifications\DataTransferObjects\Models\NotificationTriggerDTO;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Models\NotificationTrigger;

class NotificationTriggersRepository
{
    /**
     * @return NotificationTriggerDTO[]|null
     */
    public static function getAllTriggers(): ?array
    {
        $triggers = NotificationTrigger::query()->get();
        if (! is_null($triggers)) {
            foreach ($triggers as $trigger) {
                $supportShipmentStatuses = NotificationTriggerEnum::isSupportShipmentStatus(NotificationTriggerEnum::fromSlug($trigger->slug));

                $supportNoChangeLimit = NotificationTriggerEnum::isSupportNoChangeLimit(NotificationTriggerEnum::fromSlug($trigger->slug));

                $labelDTO = new TranslationDTO($trigger->label_translation_loc_id);

                $response[] = new NotificationTriggerDTO(
                    $trigger->slug,
                    $labelDTO,
                    $supportShipmentStatuses,
                    $supportNoChangeLimit
                );
            }
        } else {
            return null;
        }

        return $response;
    }

    public static function getTriggerTranslation(NotificationTriggerEnum $trigger): ?TranslationDTO
    {
        $locId = NotificationTrigger::query()->find($trigger->value)->label_translation_loc_id;

        return new TranslationDTO($locId);
    }
}

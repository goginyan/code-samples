<?php

namespace Notifications\Listeners;

use Billing\Events\BillingPlanFeaturesChangeEvent;
use Data\Enums\FeatureEnum;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Repositories\NotificationConfigurationRepository;

class BillingPlanFeaturesChangeListener
{
    /**
     * Handle the event.
     *
     * @param BillingPlanFeaturesChangeEvent $event
     * @return void
     */
    public function handle(BillingPlanFeaturesChangeEvent $event)
    {
        $notificationConfigurationRepo = new NotificationConfigurationRepository();
        // deactivate old configs
        $notificationConfigurationRepo->deactivateUnusedConfigurations($event->storeId, $event->newFeaturesIds);

        foreach ($event->newFeaturesIds as $featureId) {
            $featureEnum = FeatureEnum::make($featureId);

            $notificationChannelEnum = NotificationChannelEnum::fromFeatureEnum($featureEnum);
            if (! $notificationChannelEnum) {
                continue;
            }
            // TODO: [Improvement] this can be implement in a bulk rather 1-by-1 calls
            $notificationConfigurationRepo->getOrCreate($event->storeId, $notificationChannelEnum, true);
        }
    }
}

<?php

declare(strict_types=1);

namespace Notifications\Notifications\Channels;

use App\Enums\HTTPMethodEnum;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Illuminate\Http\Client\Response;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigKlaviyoDTO;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Notifications\Messages\KlaviyoMessage;
use Notifications\Notifications\ShipmentNotificationBase;

class KlaviyoChannel extends HTTPRequestChannel
{
    public function send($notifiable, ShipmentNotificationBase $notification): void
    {
        /**
         * @var NotifConfigKlaviyoDTO $config
         */
        $config = $notification->config;

        $data = KlaviyoMessage::EVENT($config->publicApiKey, $notification->trigger, $notification->dto, $config->language, $notification->trigger, null);
        $getDataPayload = base64_encode(json_encode($data, JSON_UNESCAPED_SLASHES));
        $this->httpVerb = HTTPMethodEnum::GET();
        $this->uri = 'https://a.klaviyo.com/api/track?data='.$getDataPayload;
        parent::send($notifiable, $notification);
    }

    protected function handleResponse(Response $response): void
    {
        /*
        if ($statusCode == 403) {
            throw new KlaviyoAuthenticationException(self::ERROR_INVALID_API_KEY);
        } elseif ($statusCode == 404) {
            throw new KlaviyoResourceNotFoundException(self::ERROR_RESOURCE_DOES_NOT_EXIST);
        } elseif ($statusCode == 429) {
            throw new KlaviyoRateLimitException($this->decodeJsonResponse($response));
        } elseif ($statusCode != 200) {
            throw new KlaviyoException(sprintf(self::ERROR_NON_200_STATUS, $statusCode));
        }*/

        $status = null;
        if ($response->status() == HTTPStatusCodeEnum::SUCCESS_OK_200()->value & $response->body() == '1') {
            $status = NotificationLogStatusEnum::SUCCESS();
        } else {
            $status = NotificationLogStatusEnum::FAILED();
        }

        parent::markCompleted($status);
    }
}

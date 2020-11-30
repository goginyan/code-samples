<?php

namespace Notifications\Notifications\Channels;

use App\Enums\HTTPMethodEnum;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Illuminate\Http\Client\Response;
use Notifications\DataTransferObjects\NotifConfig\NotifConfigSlackDTO;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Notifications\Messages\SlackMessage;
use Notifications\Notifications\ShipmentNotificationBase;

class SlackChannel extends HTTPRequestChannel
{
    public function send($notifiable, ShipmentNotificationBase $notification)
    {
        $this->httpVerb = HTTPMethodEnum::POST();

        /**
         * @var NotifConfigSlackDTO $config
         */
        $config = $notification->config;

        $this->uri = $config->webhookURL;
        $this->headers = ['Content-type' => 'application/json'];
        $this->options = SlackMessage::DEFAULT_MESSAGE($notification->dto, $config->language, $notification->trigger);

        parent::send($notifiable, $notification);
    }

    protected function handleResponse(Response $response)
    {
        $status = null;
        if ($response->status() == HTTPStatusCodeEnum::SUCCESS_OK_200()->value & $response->body() == 'ok') {
            $status = NotificationLogStatusEnum::SUCCESS();
        } else {
            $status = NotificationLogStatusEnum::FAILED();
        }

        parent::markCompleted($status);
    }
}

<?php

declare(strict_types=1);

namespace Notifications\Notifications\Channels;

use App\Enums\HTTPMethodEnum;
use App\Services\SentryService;
use App\Support\Helpers\HttpStatusCodeHelper;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Notifications\ShipmentNotificationBase;

class HTTPRequestChannel extends BaseChannel
{
    protected HTTPMethodEnum $httpVerb;
    protected string $uri;
    protected array $options;
    protected array $headers = [];

    public function send($notifiable, ShipmentNotificationBase $notification)
    {
        parent::send($notifiable, $notification);
        $response = null;
        $this->headers['User-Agent'] = 'tryrush.io';
        try {
            $pendingRequest = Http::retry(1, 100)->withHeaders($this->headers);
            if ($this->httpVerb->equals(HTTPMethodEnum::POST())) {
                $response = $pendingRequest->post($this->uri, $this->options);
            } elseif ($this->httpVerb->equals(HTTPMethodEnum::GET())) {
                $response = $pendingRequest->get($this->uri);
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }

        if (! is_null($response)) {
            $this->handleResponse($response);
        }
    }

    protected function handleException(Exception $e)
    {
        SentryService::exception($e, null, [
            'channel' => $this,
            'exception' => $e,
        ]);

        parent::markCompleted(NotificationLogStatusEnum::FAILED());
    }

    protected function handleResponse(Response $response)
    {
        $status = null;

        if (HttpStatusCodeHelper::in200Range($response->status())) {
            $status = NotificationLogStatusEnum::SUCCESS();
        } elseif (HttpStatusCodeHelper::in400Range($response->status()) || HttpStatusCodeHelper::in500Range($response->status())) {
            $status = NotificationLogStatusEnum::FAILED();
        }

        parent::markCompleted($status);
    }
}

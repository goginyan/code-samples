<?php

namespace Notifications\Notifications\Channels;

use App\Enums\HTTPMethodEnum;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use App\Services\SentryService;
use App\Services\Shopify\ApiRequestService;
use Illuminate\Http\Client\Response;
use Notifications\DataTransferObjects\NotifChannelMessageData\ShopifyFulfillmentMessageDataDTO;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Notifications\ShipmentNotificationBase;

class ShopifyFulfillmentChannel extends BaseChannel
{
    /**
     * @param $notifiable
     * @param ShipmentNotificationBase $notification
     */
    public function send($notifiable, ShipmentNotificationBase $notification)
    {
        parent::send($notifiable, $notification);
        /**
         * @var ShopifyFulfillmentMessageDataDTO $dto
         */
        $dto = $notification->dto;

        $apiRequestService = new ApiRequestService();
        $endpoint = sprintf('orders/%d/fulfillments/%d/events.json', $dto->shopifyOrderID, $dto->shopifyFulfillmentID);
        $requestUrl = $apiRequestService->endpoint($dto->myShopifyDomain, $endpoint);

        $response = $apiRequestService->makeRequest($dto->accessToken, $requestUrl, HTTPMethodEnum::POST(), $dto->toShopifyArray());

        $this->handleResponse($response);
    }

    protected function handleResponse(Response $response)
    {
        $status = null;
        if ($response->status() == HTTPStatusCodeEnum::SUCCESS_CREATED_201()->value) {
            $status = NotificationLogStatusEnum::SUCCESS();
        } else {
            $status = NotificationLogStatusEnum::FAILED();
            SentryService::dataMessage('ShopifyFulfillmentChannel sending event failure', SentryService::ERROR, null, [
                'notification' => $this->_notification,
                'body' => "Request body start -------------\n\n".$response->body()."\n\n--------------------end",
                'http_status' => $response->status(),
            ]);
        }

        parent::markCompleted($status);
    }
}

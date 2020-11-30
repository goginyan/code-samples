<?php

namespace Notifications\DataTransferObjects\NotifConfig;

use Notifications\DataTransferObjects\DatabaseJsonInterface;
use Notifications\Enums\NotificationChannelEnum;

class NotifConfigShopifyFulfillmentEventDTO extends NotifConfigBaseDTO implements DatabaseJsonInterface, IConfigDTO
{
    public bool $sendCustomerNotifications = false;

    private static string $_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS = 'send_customer_notifications';
    private static bool $_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS_DEFAULT_VALUE = false;

    public function __construct(array $parameters = [])
    {
        $this->sendCustomerNotifications = self::getValue(
            $parameters,
            self::$_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS,
            self::$_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS_DEFAULT_VALUE);

        parent::__construct(NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT());
    }

    public static function fromDatabaseJSON(array $JSONData): self
    {
        return new self($JSONData);
    }

    public function toDatabaseJSON(): string
    {
        return json_encode([
            self::$_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS => $this->sendCustomerNotifications,
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function getDefault(): self
    {
        return new self([
            self::$_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS => self::$_JSON_VAR_SEND_CUSTOMER_NOTIFICATIONS_DEFAULT_VALUE,
        ]);
    }
}

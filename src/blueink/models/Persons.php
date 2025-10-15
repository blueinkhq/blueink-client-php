<?php
namespace Blueink\ClientSDK;
/**
 * ContactChannel class
 */
class ContactChannel
{
    public ?string $email;
    public ?string $phone;
    public ?string $kind;
    public function __construct(?array $params = null)
    {
        $this->email = $params["email"] ?? null;
        $this->phone = $params["phone"] ?? null;
        $this->kind = $params["kind"] ?? null;
    }
}
/**
 * Person class
 */
class Person
{
    public ?string $name;
    public ?array $metadata;
    public ?ContactChannel $channel;
    public function __construct(?array $params = null)
    {
        $this->name = $params["name"] ?? null;
        $this->metadata = $params["metadata"] ?? null;
        $this->channel = $params["channel"] ?? null;
    }
}
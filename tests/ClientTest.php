<?php
/**
 */

use BlueInk\ApiClient\Client;
use BlueInk\ApiClient\BundleResource;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testAuthTokenRequired()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new Client(null);
    }
}

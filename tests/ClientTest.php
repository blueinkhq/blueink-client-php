<?php
/**
 */

use PHPUnit\Framework\TestCase;
use Blueink\ClientSDK\Client;

/**
 * @covers \Blueink\Client
 */class ClientTest extends TestCase
{
    public function testAuthTokenRequired()
    {
        $this->expectException(\InvalidArgumentException::class);
        $client = new Client(null);
    }
}

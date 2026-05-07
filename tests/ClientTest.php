<?php
/**
 */

use Blueink\ClientSDK\Client;
use PHPUnit\Framework\TestCase;

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

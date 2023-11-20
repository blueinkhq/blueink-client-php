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

    public function testParsePaginationHeader()
    {
        $result = Client::parsePaginationHeader("1,5,20,96");
        $this->assertEquals([
            'currentPage' => 1,
            'totalPages' => 5,
            'perPage' => 20,
            'totalResults' => 96,
        ], $result);
    }
}

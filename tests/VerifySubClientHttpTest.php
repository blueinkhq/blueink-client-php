<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use Blueink\ClientSDK\VerifySubClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\VerifySubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\VerifyEndpoints
 */
class VerifySubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    public function testCreatePostsVerifyPath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{"verified":true}')]);
        $sub = new VerifySubClient(self::BASE, $built['request']);

        $sub->create(['hash' => 'deadbeef']);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/verify/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('deadbeef', $body['hash']);
    }
}

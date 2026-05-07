<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\PacketSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\PacketSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\PacketEndpoints
 */
class PacketSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new PacketSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testUpdateIssuesPatchWithJson(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->update('pkt_1', ['name' => 'Renamed']);

        $req = $built['history'][0]['request'];
        $this->assertSame('PATCH', $req->getMethod());
        $this->assertSame(self::BASE . '/packets/pkt_1/', (string) $req->getUri());
        $this->assertSame(['name' => 'Renamed'], json_decode((string) $req->getBody(), true));
    }

    public function testEmbedURLIssuesPost(): void
    {
        $built = $this->client([new Response(200, [], '{"url":"https://embed/x"}')]);

        $resp = $built['sub']->embedURL('pkt_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/packets/pkt_1/embed_url/', (string) $req->getUri());
        $this->assertSame('https://embed/x', $resp->data['url']);
    }

    public function testRetrieveCOEIssuesGet(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->retrieveCOE('pkt_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame(self::BASE . '/packets/pkt_1/coe/', (string) $req->getUri());
    }

    public function testRemindIssuesPut(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->remind('pkt_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('PUT', $req->getMethod());
        $this->assertSame(self::BASE . '/packets/pkt_1/remind/', (string) $req->getUri());
    }
}

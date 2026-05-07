<?php
namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use Blueink\ClientSDK\WebhookSubClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\WebhookSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\WebhookEndpoints
 */
class WebhookSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new WebhookSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testCreateIssuesPostWithJson(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"wh_1"}')]);

        $built['sub']->create(['url' => 'https://hook/x', 'event_types' => ['bundle_complete']]);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/webhooks/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('https://hook/x', $body['url']);
    }

    public function testListPassesPagination(): void
    {
        $built = $this->client([new Response(200, [], '[]')]);

        $built['sub']->list(1, 5);

        parse_str($built['history'][0]['request']->getUri()->getQuery(), $q);
        $this->assertSame('1', $q['page']);
        $this->assertSame('5', $q['per_page']);
    }

    public function testRetrieveAndDelete(): void
    {
        $built = $this->client([
            new Response(200, [], '{}'),
            new Response(204),
        ]);

        $built['sub']->retrieve('wh_1');
        $built['sub']->delete('wh_1');

        $this->assertSame('GET',    $built['history'][0]['request']->getMethod());
        $this->assertSame('DELETE', $built['history'][1]['request']->getMethod());
        $this->assertSame(self::BASE . '/webhooks/wh_1/', (string) $built['history'][0]['request']->getUri());
        $this->assertSame(self::BASE . '/webhooks/wh_1/', (string) $built['history'][1]['request']->getUri());
    }

    public function testUpdatePutAndPatch(): void
    {
        $built = $this->client([
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
        ]);

        $built['sub']->update('wh_1', ['enabled' => true]);
        $built['sub']->update('wh_1', ['enabled' => false], true);

        $this->assertSame('PUT',   $built['history'][0]['request']->getMethod());
        $this->assertSame('PATCH', $built['history'][1]['request']->getMethod());
    }

    public function testHeaderCRUD(): void
    {
        $built = $this->client([
            new Response(201, [], '{"id":"hdr_1"}'),
            new Response(200, [], '[]'),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(204),
        ]);

        $built['sub']->createHeader(['name' => 'X-Foo', 'value' => 'bar']);
        $built['sub']->listHeaders(2, 10);
        $built['sub']->retrieveHeader('hdr_1');
        $built['sub']->updateHeader('hdr_1', ['value' => 'baz']);
        $built['sub']->updateHeader('hdr_1', ['value' => 'baz2'], true);
        $built['sub']->deleteHeader('hdr_1');

        $h = $built['history'];
        $this->assertSame('POST',  $h[0]['request']->getMethod());
        $this->assertSame(self::BASE . '/webhooks/headers/', (string) $h[0]['request']->getUri());
        $this->assertSame('GET', $h[1]['request']->getMethod());
        parse_str($h[1]['request']->getUri()->getQuery(), $q);
        $this->assertSame('2', $q['page']);
        $this->assertSame('GET',    $h[2]['request']->getMethod());
        $this->assertSame('PUT',    $h[3]['request']->getMethod());
        $this->assertSame('PATCH',  $h[4]['request']->getMethod());
        $this->assertSame('DELETE', $h[5]['request']->getMethod());
        $this->assertSame(self::BASE . '/webhooks/headers/hdr_1/', (string) $h[5]['request']->getUri());
    }

    public function testEventsAndDeliveries(): void
    {
        $built = $this->client([
            new Response(200, [], '[]'),
            new Response(200, [], '{}'),
            new Response(200, [], '[]'),
            new Response(200, [], '{}'),
        ]);

        $built['sub']->listEvents(1, 50);
        $built['sub']->retrieveEvent('evt_1');
        $built['sub']->listDeliveries(1, 50);
        $built['sub']->retrieveDelivery('del_1');

        $h = $built['history'];
        $this->assertSame(self::BASE . '/webhooks/events/',         $h[0]['request']->getUri()->getScheme() . '://' . $h[0]['request']->getUri()->getHost() . $h[0]['request']->getUri()->getPath());
        $this->assertSame(self::BASE . '/webhooks/events/evt_1/',   (string) $h[1]['request']->getUri());
        $this->assertSame(self::BASE . '/webhooks/deliveries/',     $h[2]['request']->getUri()->getScheme() . '://' . $h[2]['request']->getUri()->getHost() . $h[2]['request']->getUri()->getPath());
        $this->assertSame(self::BASE . '/webhooks/deliveries/del_1/', (string) $h[3]['request']->getUri());
    }

    public function testRetrieveSecretGetAndRegeneratePost(): void
    {
        $built = $this->client([
            new Response(200, [], '{"secret":"abc"}'),
            new Response(200, [], '{"secret":"def"}'),
        ]);

        $built['sub']->retrieveSecret();
        $built['sub']->regenerateSecret();

        $h = $built['history'];
        $this->assertSame('GET',  $h[0]['request']->getMethod());
        $this->assertSame(self::BASE . '/webhooks/secret/', (string) $h[0]['request']->getUri());
        $this->assertSame('POST', $h[1]['request']->getMethod());
        $this->assertSame(self::BASE . '/webhooks/secret/regenerate/', (string) $h[1]['request']->getUri());
    }
}

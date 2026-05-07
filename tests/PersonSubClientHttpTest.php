<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\PersonHelper;
use Blueink\ClientSDK\PersonSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\PersonSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\PersonEndpoints
 */
class PersonSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new PersonSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testCreateRequiresName(): void
    {
        $built = $this->client([]);

        $this->expectException(\InvalidArgumentException::class);
        $built['sub']->create(['metadata' => ['x' => 1]]);
    }

    public function testCreateSendsJsonPost(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"per_1"}')]);

        $built['sub']->create(['name' => 'Jane Doe']);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/persons/', (string) $req->getUri());
        $this->assertSame(['name' => 'Jane Doe'], json_decode((string) $req->getBody(), true));
    }

    public function testCreateMergesAdditionalData(): void
    {
        $built = $this->client([new Response(201, [], '{}')]);

        $built['sub']->create(['name' => 'Jane'], ['metadata' => ['team' => 'A']]);

        $sent = json_decode((string) $built['history'][0]['request']->getBody(), true);
        $this->assertSame('Jane', $sent['name']);
        $this->assertSame(['team' => 'A'], $sent['metadata']);
    }

    public function testCreateFromPersonHelperUsesAsArray(): void
    {
        $built = $this->client([new Response(201, [], '{}')]);
        $helper = new PersonHelper([
            'name'   => 'Helper Person',
            'emails' => ['a@example.com'],
            'phones' => ['+15551234567'],
        ]);

        $built['sub']->createFromPersonHelper($helper);

        $sent = json_decode((string) $built['history'][0]['request']->getBody(), true);
        $this->assertSame('Helper Person', $sent['name']);
        $this->assertCount(2, $sent['channels']);
        $this->assertSame(['email' => 'a@example.com', 'kind' => 'em'], $sent['channels'][0]);
    }

    public function testListPassesPagination(): void
    {
        $built = $this->client([new Response(200, [], '[]')]);

        $built['sub']->list(3, 10, ['team' => 'A']);

        parse_str($built['history'][0]['request']->getUri()->getQuery(), $q);
        $this->assertSame('3', $q['page']);
        $this->assertSame('10', $q['per_page']);
        $this->assertSame('A', $q['team']);
    }

    public function testRetrieveHitsPersonPath(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->retrieve('per_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame(self::BASE . '/persons/per_1/', (string) $req->getUri());
    }

    public function testUpdateUsesPutByDefault(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->update('per_1', ['name' => 'New']);

        $req = $built['history'][0]['request'];
        $this->assertSame('PUT', $req->getMethod());
        $this->assertSame(['name' => 'New'], json_decode((string) $req->getBody(), true));
    }

    public function testUpdatePartialUsesPatch(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->update('per_1', ['name' => 'Partial'], true);

        $this->assertSame('PATCH', $built['history'][0]['request']->getMethod());
    }

    public function testDeleteIssuesDelete(): void
    {
        $built = $this->client([new Response(204)]);

        $built['sub']->delete('per_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('DELETE', $req->getMethod());
        $this->assertSame(self::BASE . '/persons/per_1/', (string) $req->getUri());
    }
}

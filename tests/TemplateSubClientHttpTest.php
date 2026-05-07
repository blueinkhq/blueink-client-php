<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\TemplateSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\TemplateSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\TemplateEndpoints
 */
class TemplateSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new TemplateSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testListPassesPaginationAsQuery(): void
    {
        $built = $this->client([new Response(200, [], '[]')]);

        $built['sub']->list(2, 50);

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        parse_str($req->getUri()->getQuery(), $q);
        $this->assertSame('2', $q['page']);
        $this->assertSame('50', $q['per_page']);
        $this->assertSame(self::BASE . '/templates/', $req->getUri()->getScheme() . '://' . $req->getUri()->getHost() . $req->getUri()->getPath());
    }

    public function testRetrieveHitsTemplatePath(): void
    {
        $built = $this->client([new Response(200, [], '{"id":"tpl_1"}')]);

        $built['sub']->retrieve('tpl_1');

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame(self::BASE . '/templates/tpl_1/', (string) $req->getUri());
    }
}

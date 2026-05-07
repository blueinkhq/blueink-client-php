<?php
namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\EnvelopeTemplateSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\EnvelopeTemplateSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\EnvelopeTemplateEndpoints
 */
class EnvelopeTemplateSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new EnvelopeTemplateSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testListPassesPaginationAsQuery(): void
    {
        $built = $this->client([new Response(200, [], '[]')]);

        $built['sub']->list(3, 25);

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        parse_str($req->getUri()->getQuery(), $q);
        $this->assertSame('3',  $q['page']);
        $this->assertSame('25', $q['per_page']);
        $this->assertSame(
            self::BASE . '/envelope-templates/',
            $req->getUri()->getScheme() . '://' . $req->getUri()->getHost() . $req->getUri()->getPath()
        );
    }

    public function testRetrieveHitsEnvelopeTemplatePath(): void
    {
        $built = $this->client([new Response(200, [], '{"id":"T-abc"}')]);

        $built['sub']->retrieve('T-abc');

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame(self::BASE . '/envelope-templates/T-abc/', (string) $req->getUri());
    }

    public function testPagedListReturnsLazyIterator(): void
    {
        $built = $this->client([
            new Response(
                200,
                ['X-Blueink-Pagination' => '1,1,1,1'],
                '[{"id":"T-1"}]'
            ),
        ]);

        $paginated = $built['sub']->pagedList(1, 50);

        $pages = iterator_to_array($paginated, false);
        $this->assertCount(1, $pages);
        $this->assertSame('T-1', $pages[0]->data[0]['id']);
    }
}

<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\NormalizedResponse;
use Blueink\ClientSDK\Pagination;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\NormalizedResponse
 * @covers \Blueink\ClientSDK\Pagination
 */
class NormalizedResponseTest extends TestCase
{
    public function testParsesJsonBody(): void
    {
        $payload = ['id' => 'abc', 'status' => 'co'];
        $resp = new Response(200, ['Content-Type' => 'application/json'], json_encode($payload));

        $normalized = new NormalizedResponse($resp);

        $this->assertSame(200, $normalized->status);
        $this->assertSame($payload, $normalized->data);
    }

    public function testNonJsonBodyKeptAsString(): void
    {
        $resp = new Response(200, ['Content-Type' => 'text/plain'], 'hello');

        $normalized = new NormalizedResponse($resp);

        $this->assertSame('hello', $normalized->data);
    }

    public function testEmptyBodyIsNull(): void
    {
        $resp = new Response(204);

        $normalized = new NormalizedResponse($resp);

        $this->assertNull($normalized->data);
        $this->assertSame(204, $normalized->status);
    }

    public function testHeadersExposedAsArray(): void
    {
        $resp = new Response(200, ['X-Foo' => 'bar', 'Content-Type' => 'application/json'], '{}');

        $normalized = new NormalizedResponse($resp);

        $this->assertSame('bar', $normalized->headers['X-Foo']);
        $this->assertSame('application/json', $normalized->headers['Content-Type']);
    }

    public function testPaginationParsedFromHeader(): void
    {
        $resp = new Response(
            200,
            ['X-Blueink-Pagination' => '2,5,25,125', 'Content-Type' => 'application/json'],
            '[]'
        );

        $normalized = new NormalizedResponse($resp);

        $this->assertInstanceOf(Pagination::class, $normalized->pagination);
        $this->assertSame(2, $normalized->pagination->page_number);
        $this->assertSame(5, $normalized->pagination->total_pages);
        $this->assertSame(25, $normalized->pagination->per_page);
        $this->assertSame(125, $normalized->pagination->total_results);
    }

    public function testPaginationNullWhenHeaderAbsent(): void
    {
        $resp = new Response(200, ['Content-Type' => 'application/json'], '[]');

        $normalized = new NormalizedResponse($resp);

        $this->assertNull($normalized->pagination);
    }

    public function testOriginalResponseExposed(): void
    {
        $resp = new Response(200, [], '{}');

        $normalized = new NormalizedResponse($resp);

        $this->assertSame($resp, $normalized->originalResponse);
    }
}

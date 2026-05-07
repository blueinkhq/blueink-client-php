<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\BlueinkApiError;
use Blueink\ClientSDK\NormalizedResponse;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\RequestHelper
 * @covers \Blueink\ClientSDK\NormalizedResponse
 */
class RequestHelperHttpTest extends TestCase
{
    public function testGetSendsAuthorizationHeader(): void
    {
        $built = MockHttpFactory::buildRequestHelper([
            new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
        ], 'secret_key');

        $built['request']->get('https://api.example.com/x');

        /** @var \Psr\Http\Message\RequestInterface $req */
        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame('TOKEN secret_key', $req->getHeaderLine('Authorization'));
    }

    public function testGetEncodesQueryParams(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{}')]);

        $built['request']->get('https://api.example.com/bundles/', [
            'query' => ['page' => 2, 'per_page' => 25, 'status' => 'co'],
        ]);

        $req = $built['history'][0]['request'];
        parse_str($req->getUri()->getQuery(), $parsed);
        $this->assertSame(['page' => '2', 'per_page' => '25', 'status' => 'co'], $parsed);
    }

    public function testParamsAliasIsAcceptedAsQuery(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{}')]);

        $built['request']->get('https://api.example.com/x', ['params' => ['a' => 1]]);

        $this->assertStringContainsString('a=1', (string) $built['history'][0]['request']->getUri());
    }

    public function testPostJsonSerializesBodyAndSetsContentType(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(201, [], '{}')]);
        $payload = ['label' => 'Test', 'is_test' => true];

        $built['request']->post('https://api.example.com/bundles/', ['json' => $payload]);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertStringContainsString('application/json', $req->getHeaderLine('Content-Type'));
        $this->assertSame($payload, json_decode((string) $req->getBody(), true));
    }

    public function testMultipartRequestUsesMultipartContentType(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(201, [], '{}')]);

        $built['request']->post('https://api.example.com/bundles/', [
            'multipart' => [
                ['name' => 'bundle_request', 'contents' => '{"a":1}', 'headers' => ['Content-Type' => 'application/json']],
                ['name' => 'files[0]', 'contents' => 'binary-bytes', 'filename' => 'a.pdf', 'headers' => ['Content-Type' => 'application/pdf']],
            ],
        ]);

        $req = $built['history'][0]['request'];
        $this->assertStringStartsWith('multipart/form-data', $req->getHeaderLine('Content-Type'));
        $body = (string) $req->getBody();
        $this->assertStringContainsString('name="bundle_request"', $body);
        $this->assertStringContainsString('filename="a.pdf"', $body);
    }

    public function testPutPatchDeleteVerbs(): void
    {
        $built = MockHttpFactory::buildRequestHelper([
            new Response(200, [], '{}'),
            new Response(200, [], '{}'),
            new Response(204),
        ]);
        $req = $built['request'];

        $req->put('https://api.example.com/x/1/', ['json' => ['a' => 1]]);
        $req->patch('https://api.example.com/x/1/', ['json' => ['b' => 2]]);
        $req->delete('https://api.example.com/x/1/');

        $this->assertSame('PUT', $built['history'][0]['request']->getMethod());
        $this->assertSame('PATCH', $built['history'][1]['request']->getMethod());
        $this->assertSame('DELETE', $built['history'][2]['request']->getMethod());
    }

    public function testRaisesByDefaultOn4XX(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(404, [], '{"detail":"nope"}')]);

        $this->expectException(BlueinkApiError::class);
        $built['request']->get('https://api.example.com/missing/');
    }

    public function testRaisedExceptionWrapsGuzzleAsPrevious(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(404, [], '{"detail":"nope"}')]);

        try {
            $built['request']->get('https://api.example.com/missing/');
            $this->fail('Expected BlueinkApiError to be thrown');
        } catch (BlueinkApiError $e) {
            $this->assertSame(404, $e->status_code);
            $this->assertSame('nope', $e->detail);
            $this->assertInstanceOf(ClientException::class, $e->getPrevious());
        }
    }

    public function testRaisedExceptionParsesStructuredErrorBody(): void
    {
        $body = json_encode([
            'detail' => 'Invalid input.',
            'code'   => 'invalid',
            'errors' => [
                ['field' => 'channels', 'message' => 'This field is required.'],
                ['field' => 'name',     'message' => 'Cannot be blank.'],
            ],
        ]);
        $built = MockHttpFactory::buildRequestHelper([new Response(400, [], $body)]);

        try {
            $built['request']->post('https://api.example.com/persons/', ['json' => []]);
            $this->fail('Expected BlueinkApiError to be thrown');
        } catch (BlueinkApiError $e) {
            $this->assertSame(400, $e->status_code);
            $this->assertSame('Invalid input.', $e->detail);
            $this->assertSame('invalid', $e->api_code);
            $this->assertCount(2, $e->errors);
            $this->assertSame('channels', $e->errors[0]['field']);
            $this->assertStringContainsString('400', $e->getMessage());
            $this->assertStringContainsString('Invalid input.', $e->getMessage());
            $this->assertStringContainsString('channels: This field is required.', $e->getMessage());
        }
    }

    public function testRaisedExceptionHandlesNonJsonErrorBody(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(500, [], '<html>boom</html>')]);

        try {
            $built['request']->get('https://api.example.com/x/');
            $this->fail('Expected BlueinkApiError to be thrown');
        } catch (BlueinkApiError $e) {
            $this->assertSame(500, $e->status_code);
            $this->assertNull($e->detail);
            $this->assertNull($e->api_code);
            $this->assertSame([], $e->errors);
            $this->assertSame('<html>boom</html>', $e->body);
        }
    }

    public function testReturnsNormalizedResponseWhenRaiseExceptionsFalse(): void
    {
        $built = MockHttpFactory::buildRequestHelper(
            [new Response(404, [], '{"detail":"nope"}')],
            'k',
            false
        );

        $resp = $built['request']->get('https://api.example.com/missing/');

        $this->assertInstanceOf(NormalizedResponse::class, $resp);
        $this->assertSame(404, $resp->status);
        $this->assertSame(['detail' => 'nope'], $resp->data);
    }

    public function testGetLastResponseTracksMostRecent(): void
    {
        $built = MockHttpFactory::buildRequestHelper([
            new Response(200, [], '{"a":1}'),
            new Response(200, [], '{"b":2}'),
        ]);

        $built['request']->get('https://api.example.com/a/');
        $built['request']->get('https://api.example.com/b/');

        $this->assertSame(['b' => 2], $built['request']->getLastResponse()->data);
    }

    public function testCustomHeadersMergedAlongsideAuth(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{}')]);

        $built['request']->get('https://api.example.com/x', [
            'headers' => ['X-Custom' => 'yes'],
        ]);

        $req = $built['history'][0]['request'];
        $this->assertSame('yes', $req->getHeaderLine('X-Custom'));
        $this->assertNotEmpty($req->getHeaderLine('Authorization'));
    }
}

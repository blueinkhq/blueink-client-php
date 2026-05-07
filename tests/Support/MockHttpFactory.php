<?php
namespace Blueink\ClientSDK\Tests\Support;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Blueink\ClientSDK\RequestHelper;

/**
 * Wire a Guzzle client to a MockHandler queue and capture every outbound
 * request in a history array. Returned tuple lets each test assert against
 * exact verb/URL/headers/body without round-tripping the network.
 */
class MockHttpFactory
{
    /**
     * @param array $responses ordered queue of \GuzzleHttp\Psr7\Response (or RequestException)
     * @return array{guzzle: GuzzleClient, history: array}
     */
    public static function build(array $responses): array
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $history = [];
        $stack->push(Middleware::history($history));
        $guzzle = new GuzzleClient(['handler' => $stack]);

        return ['guzzle' => $guzzle, 'history' => &$history];
    }

    /**
     * Convenience: build a RequestHelper backed by a mocked Guzzle client.
     *
     * @return array{request: RequestHelper, history: array}
     */
    public static function buildRequestHelper(array $responses, string $api_key = 'test_key', bool $raise_exceptions = true): array
    {
        $built = self::build($responses);
        $request = new RequestHelper($api_key, $raise_exceptions, $built['guzzle']);

        return ['request' => $request, 'history' => &$built['history']];
    }
}

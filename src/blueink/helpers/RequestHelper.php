<?php

namespace Blueink\ClientSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Thin wrapper around Guzzle that:
 *   - injects the Authorization header on every request
 *   - normalizes call options to Guzzle's request options
 *     (json, multipart, query, headers, body)
 *   - returns a NormalizedResponse instead of a raw PSR-7 response
 *   - optionally swallows 4XX/5XX errors and surfaces them as
 *     NormalizedResponse objects instead (raise_exceptions=false)
 */
class RequestHelper
{
    protected string $private_api_key;
    protected bool $raise_exceptions;
    protected ?Client $client;
    private ?NormalizedResponse $last_response = null;

    public function __construct(string $private_api_key, bool $raise_exceptions = true, ?Client $client = null)
    {
        if ($private_api_key === '' || trim($private_api_key) === '') {
            // Allowed for back-compat with existing constructor tests; auth will fail at call time.
        }
        $this->private_api_key = $private_api_key;
        $this->raise_exceptions = $raise_exceptions;
        $this->client = $client;
    }

    public function get(string $url, ?array $options = null): NormalizedResponse
    {
        return $this->makeRequest('GET', $url, $options);
    }

    public function post(string $url, ?array $options = null): NormalizedResponse
    {
        return $this->makeRequest('POST', $url, $options);
    }

    public function put(string $url, ?array $options = null): NormalizedResponse
    {
        return $this->makeRequest('PUT', $url, $options);
    }

    public function patch(string $url, ?array $options = null): NormalizedResponse
    {
        return $this->makeRequest('PATCH', $url, $options);
    }

    public function delete(string $url, ?array $options = null): NormalizedResponse
    {
        return $this->makeRequest('DELETE', $url, $options);
    }

    public function getLastResponse(): ?NormalizedResponse
    {
        return $this->last_response;
    }

    private function makeRequest(string $method, string $url, ?array $options = null): NormalizedResponse
    {
        $guzzle_options = $this->buildOptions($options ?? []);
        $client = $this->client ?? new Client();

        try {
            $response = $client->request($method, $url, $guzzle_options);
        } catch (BadResponseException $e) {
            if ($this->raise_exceptions) {
                throw BlueinkApiError::fromBadResponseException($e);
            }
            $response = $e->getResponse();
        }

        $normalized = new NormalizedResponse($response);
        $this->last_response = $normalized;

        return $normalized;
    }

    /**
     * Translate the SDK's option array into Guzzle request options.
     * Recognized keys: json, multipart, body, query, params, headers, content_type.
     * `params` is accepted as a back-compat alias for `query`.
     */
    private function buildOptions(array $options): array
    {
        $guzzle_options = [];

        $content_type = $options['content_type'] ?? null;
        $headers = $options['headers'] ?? [];
        $guzzle_options['headers'] = $this->buildHeaders($content_type, $headers, isset($options['multipart']));

        if (array_key_exists('json', $options) && $options['json'] !== null) {
            $guzzle_options['json'] = $options['json'];
        } elseif (array_key_exists('multipart', $options) && $options['multipart'] !== null) {
            $guzzle_options['multipart'] = $options['multipart'];
        } elseif (array_key_exists('body', $options) && $options['body'] !== null) {
            $body = $options['body'];
            if (is_object($body)) {
                $body = Helper::removeNullProperties($body);
                $guzzle_options['json'] = $body;
            } elseif (is_array($body)) {
                $guzzle_options['json'] = $body;
            } else {
                $guzzle_options['body'] = $body;
            }
        }

        $query = $options['query'] ?? $options['params'] ?? null;
        if (is_array($query) && !empty($query)) {
            $guzzle_options['query'] = $query;
        }

        return $guzzle_options;
    }

    private function buildHeaders(?string $content_type, array $extra_headers, bool $is_multipart): array
    {
        $headers = ['Authorization' => TOKEN . $this->private_api_key];
        // Guzzle sets the multipart Content-Type (with boundary) itself.
        if (!$is_multipart) {
            $headers['Content-Type'] = $content_type ?? 'application/json';
        }
        if (!empty($extra_headers)) {
            $headers = Helper::mergeAdditionalData($headers, $extra_headers);
        }

        return $headers;
    }
}

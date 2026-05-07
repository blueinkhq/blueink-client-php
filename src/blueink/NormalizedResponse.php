<?php
namespace Blueink\ClientSDK;

use Psr\Http\Message\ResponseInterface;

/**
 * Wraps a PSR-7 response from a Blueink REST endpoint.
 *
 * Mirrors the Python SDK's NormalizedResponse:
 *   - `data`     parsed JSON body (associative array) or raw body string when not JSON
 *   - `status`   HTTP status code
 *   - `headers`  associative array of response headers (last value per name)
 *   - `pagination` Pagination|null parsed from X-Blueink-Pagination
 *   - `originalResponse` the underlying PSR-7 ResponseInterface
 */
class NormalizedResponse
{
    public mixed $data;
    public int $status;
    public array $headers;
    public ?Pagination $pagination = null;
    public ResponseInterface $originalResponse;

    public function __construct(ResponseInterface $response)
    {
        $this->originalResponse = $response;
        $this->status = $response->getStatusCode();

        $body = (string) $response->getBody();
        if ($body === '') {
            $this->data = null;
        } else {
            $decoded = json_decode($body, true);
            $this->data = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $body;
        }

        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = end($values);
        }
        $this->headers = $headers;

        if ($response->hasHeader(BLUEINK_PAGINATION_HEADER)) {
            $this->pagination = new Pagination(
                $response->getHeaderLine(BLUEINK_PAGINATION_HEADER)
            );
        }
    }
}

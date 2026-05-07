<?php

namespace Blueink\ClientSDK;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Domain-specific exception for 4XX/5XX responses from the Blueink API.
 *
 * Parses the standard error body shape:
 *   { "detail": "...", "code": "...", "errors": [ { "field": "...", "message": "..." } ] }
 *
 * The original Guzzle BadResponseException is preserved as the previous
 * exception so callers can still inspect the raw PSR-7 response if needed.
 */
class BlueinkApiError extends \RuntimeException
{
    public int $status_code;
    public ?string $detail;
    public ?string $api_code;
    /** @var array<int,array<string,mixed>> */
    public array $errors;
    /** @var array<string,mixed>|string|null */
    public mixed $body;
    public ?ResponseInterface $response;
    public ?RequestInterface $request;

    /**
     * @param array<int,array<string,mixed>> $errors
     * @param array<string,mixed>|string|null $body
     */
    public function __construct(
        string $message,
        int $status_code,
        ?string $detail = null,
        ?string $api_code = null,
        array $errors = [],
        mixed $body = null,
        ?ResponseInterface $response = null,
        ?RequestInterface $request = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $status_code, $previous);
        $this->status_code = $status_code;
        $this->detail = $detail;
        $this->api_code = $api_code;
        $this->errors = $errors;
        $this->body = $body;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Build a BlueinkApiError from a Guzzle BadResponseException, parsing the
     * response body for the structured fields.
     */
    public static function fromBadResponseException(BadResponseException $e): self
    {
        $response = $e->getResponse();
        $request  = $e->getRequest();

        return self::fromResponse($response, $request, $e);
    }

    /**
     * Build a BlueinkApiError from a PSR-7 response. Useful when
     * raise_exceptions=false and the caller wants to escalate later.
     */
    public static function fromResponse(
        ResponseInterface $response,
        ?RequestInterface $request = null,
        ?\Throwable $previous = null
    ): self {
        $status_code = $response->getStatusCode();
        $raw_body    = (string) $response->getBody();
        $body        = self::decodeBody($raw_body);

        $detail   = is_array($body) && isset($body['detail'])  && is_string($body['detail']) ? $body['detail'] : null;
        $api_code = is_array($body) && isset($body['code'])    && is_string($body['code']) ? $body['code'] : null;
        $errors   = is_array($body) && isset($body['errors'])  && is_array($body['errors']) ? array_values($body['errors']) : [];

        $message = self::buildMessage($status_code, $detail, $api_code, $errors, $request);

        return new self(
            message: $message,
            status_code: $status_code,
            detail: $detail,
            api_code: $api_code,
            errors: $errors,
            body: $body ?? $raw_body,
            response: $response,
            request: $request,
            previous: $previous,
        );
    }

    /**
     * @return array<string,mixed>|string|null
     */
    private static function decodeBody(string $raw): array|string|null
    {
        if ($raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return $raw;
    }

    /**
     * @param array<int,array<string,mixed>> $errors
     */
    private static function buildMessage(
        int $status_code,
        ?string $detail,
        ?string $api_code,
        array $errors,
        ?RequestInterface $request
    ): string {
        $parts = ["Blueink API error {$status_code}"];
        if ($request !== null) {
            $parts[] = $request->getMethod() . ' ' . (string) $request->getUri();
        }

        $summary = $detail ?? ($api_code !== null ? "code={$api_code}" : 'no detail provided');
        $parts[] = $summary;

        if (!empty($errors)) {
            $field_summaries = [];
            foreach ($errors as $err) {
                if (!is_array($err)) {
                    continue;
                }
                $field   = isset($err['field'])   && is_string($err['field']) ? $err['field'] : null;
                $err_msg = isset($err['message']) && is_string($err['message']) ? $err['message'] : null;
                if ($field !== null && $err_msg !== null) {
                    $field_summaries[] = "{$field}: {$err_msg}";
                } elseif ($err_msg !== null) {
                    $field_summaries[] = $err_msg;
                }
            }
            if (!empty($field_summaries)) {
                $parts[] = '(' . implode('; ', $field_summaries) . ')';
            }
        }

        return implode(' — ', $parts);
    }
}

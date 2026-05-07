<?php

namespace Blueink\ClientSDK;

class SubClient
{
    public string $base_url;
    protected RequestHelper $request;

    public function __construct(string $base_url, RequestHelper $request)
    {
        $this->base_url = rtrim($base_url, '/');
        $this->request = $request;
    }

    /**
     * Return the underlying RequestHelper, exposed so callers can reach
     * helpers like getLastResponse().
     */
    public function getRequest(): RequestHelper
    {
        return $this->request;
    }

    /**
     * Build the {page, per_page, ...$additional_params} query map sent to
     * paginated list endpoints. Either pagination key is omitted when null.
     */
    protected function buildParams(?int $page = null, ?int $per_page = null, ?array $additional_params = null): array
    {
        $params = $additional_params ?? [];

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        if (!is_null($per_page)) {
            $params['per_page'] = $per_page;
        }

        return $params;
    }

    /**
     * Build the absolute request URL by joining the configured base_url with
     * the endpoint path. Query strings are passed via Guzzle options (`query`),
     * not appended here.
     */
    protected function buildURL(string $endpoint): string
    {
        return $this->base_url . $endpoint;
    }
}

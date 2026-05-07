<?php

namespace Blueink\ClientSDK;

class WebhookSubClient extends SubClient
{
    public function create(array $data): NormalizedResponse
    {
        return $this->request->post($this->buildURL(WebhookEndpoints::create()), ['json' => $data]);
    }

    public function list(?int $page = null, ?int $per_page = null, ?array $query_params = null): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::list());
        $params = $this->buildParams($page, $per_page, $query_params);

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieve(string $webhook_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(WebhookEndpoints::retrieve($webhook_id)));
    }

    public function delete(string $webhook_id): NormalizedResponse
    {
        return $this->request->delete($this->buildURL(WebhookEndpoints::delete($webhook_id)));
    }

    public function update(string $webhook_id, array $data, bool $partial = false): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::update($webhook_id));
        $options = ['json' => $data];

        return $partial ? $this->request->patch($url, $options) : $this->request->put($url, $options);
    }

    public function createHeader(array $data): NormalizedResponse
    {
        return $this->request->post($this->buildURL(WebhookEndpoints::createHeader()), ['json' => $data]);
    }

    public function listHeaders(?int $page = null, ?int $per_page = null, ?array $query_params = null): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::listHeaders());
        $params = $this->buildParams($page, $per_page, $query_params);

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieveHeader(string $header_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(WebhookEndpoints::retrieveHeader($header_id)));
    }

    public function updateHeader(string $header_id, array $data, bool $partial = false): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::updateHeader($header_id));
        $options = ['json' => $data];

        return $partial ? $this->request->patch($url, $options) : $this->request->put($url, $options);
    }

    public function deleteHeader(string $header_id): NormalizedResponse
    {
        return $this->request->delete($this->buildURL(WebhookEndpoints::deleteHeader($header_id)));
    }

    public function listEvents(?int $page = null, ?int $per_page = null, ?array $query_params = null): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::listEvents());
        $params = $this->buildParams($page, $per_page, $query_params);

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieveEvent(string $event_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(WebhookEndpoints::retrieveEvent($event_id)));
    }

    public function listDeliveries(?int $page = null, ?int $per_page = null, ?array $query_params = null): NormalizedResponse
    {
        $url = $this->buildURL(WebhookEndpoints::listDeliveries());
        $params = $this->buildParams($page, $per_page, $query_params);

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieveDelivery(string $delivery_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(WebhookEndpoints::retrieveDelivery($delivery_id)));
    }

    public function retrieveSecret(): NormalizedResponse
    {
        return $this->request->get($this->buildURL(WebhookEndpoints::retrieveSecret()));
    }

    /**
     * Generate a new shared secret and return it. Per the Blueink API this is
     * a POST, not a GET.
     */
    public function regenerateSecret(): NormalizedResponse
    {
        return $this->request->post($this->buildURL(WebhookEndpoints::regenerateSecret()));
    }
}

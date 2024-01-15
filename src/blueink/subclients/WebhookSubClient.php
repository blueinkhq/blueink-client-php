<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/SubClient.php';
require_once __DIR__ . "//../Endpoints.php";
require_once __DIR__ . "/../helpers/Helper.php";
class WebhookSubClient extends SubClient {
    /**
     * Create webhook
     * 
     * @param array $data: array of webhook
     * 
     * @return mixed response of the request
     */
    public function create(array $data) {
        $url = parent::buildURL(WebhookEndpoints::create());

        return parent::$request->post($url, $data);
    }
    /**
     * List webhooks
     * 
     * @param ?int $page: current page, default null
     * @param ?int $per_page: number of items, default null
     * @param ?array $query_params: the query params
     * 
     * @return mixed response of the request
     */
    public function list(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::buildURL(WebhookEndpoints::list());
        $params = parent::buildParams($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve a webhook
     * 
     * @param string $webhook_id: webhook ID
     * 
     * @return mixed response of the request
     */
    public function retrieve(string $webhook_id) {
        $url = parent::buildURL(WebhookEndpoints::retrieve($webhook_id));

        return parent::$request->get($url);
    }
    /**
     * Delete a webhook
     * 
     * @param string $webhook_id: webhook ID
     * 
     * @return mixed response of the request
     */
    public function delete(string $webhook_id) {
        $url = parent::buildURL(WebhookEndpoints::delete($webhook_id));

        return parent::$request->delete($url);       
    }
    /**
     * Update a webhook
     * 
     * @param string $webhook_id: webhook ID
     * @param ?array $data: data of the webhook as [key => value]
     * 
     * @return mixed response of the request
     */
    public function update(string $webhook_id, array $data) {
        $url = parent::buildURL(WebhookEndpoints::update($webhook_id));

        return parent::$request->patch($url, $data);
    }
    /**
     * Create webhook header
     * 
     * @param array $data: data of header
     * 
     * @return mixed response of the request
     */
    public function createHeader(array $data) {
        $url = parent::buildURL(WebhookEndpoints::createHeader());

        return parent::$request->post($url, $data);
    }
    /**
     * List all headers
     * 
     * @param ?int $page: current page, default null
     * @param ?int $per_page: number of items per page, default null
     * @param ?array $query_params: query params, default null
     * 
     * @return mixed response of the request
     */
    public function listHeaders(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::buildURL(WebhookEndpoints::listHeaders());
        $params = parent::buildParams($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve header
     * 
     * @param string $header_id: header ID
     * 
     * @return mixed response of the request
     */
    public function retrieveHeader(string $header_id) {
        $url = parent::buildURL(WebhookEndpoints::retrieveHeader($header_id));

        return parent::$request->get($url);
    }
    /**
     * Delete a header
     * 
     * @param string $header_id: header ID
     * 
     * @return mixed response of the request
     */
    public function deleteHeader(string $header_id) {
        $url = parent::buildURL(WebhookEndpoints::deleteHeader($header_id));

        return parent::$request->delete($url);
    }
    /**
     * List all events
     * 
     * @param ?int $page: current page, default null
     * @param ?int $per_page: number of items per page, default null
     * @param array $query_params: query params
     * 
     * @return mixed response of the request
     */
    public function listEvents(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::buildURL(WebhookEndpoints::listEvents());
        $params = parent::buildParams($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve an event
     * 
     * @param string $event_id: event ID
     * 
     * @return mixed response of the request
     */
    public function retrieveEvent(string $event_id) {
        $url = parent::buildURL(WebhookEndpoints::retrieveEvent($event_id));

        return parent::$request->get($url);
    }
    /**
     * List all deliveries
     * 
     * @param ?int $page: current page, default null
     * @param ?int $per_page: number of items per page, default null
     * @param array $query_params: query params
     * 
     * @return mixed response of the request
     */
    public function listDeliveries(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::buildURL(WebhookEndpoints::listDeliveries());
        $params = parent::buildParams($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * retrieveDelivery
     * 
     * @param string $delivery_id: delivery ID
     * 
     * @return mixed response of the request
     */
    public function retrieveDelivery(string $delivery_id) {
        $url = parent::buildURL(WebhookEndpoints::retrieveDelivery($delivery_id));

        return parent::$request->get($url);
    }
    /**
     * Retrieve secret
     * 
     * @return mixed response of the request
     */
    public function retrieveSecret() {
        $url = parent::buildURL(WebhookEndpoints::retrieveSecret());

        return parent::$request->get($url);
    }
    /**
     * Retrieve secret after regenerate
     * 
     * @return mixed response of the request
     */
    public function regenerateSecret() {
        $url = parent::buildURL(WebhookEndpoints::regenerateSecret());

        return parent::$request->get($url);
    }
}
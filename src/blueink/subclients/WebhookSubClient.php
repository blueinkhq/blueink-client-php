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
        $url = parent::build_url(WebhookEndpoints::create());

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
        $url = parent::build_url(WebhookEndpoints::list());
        $params = parent::build_params($page, $per_page, $query_params);

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
        $url = parent::build_url(WebhookEndpoints::retrieve($webhook_id));

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
        $url = parent::build_url(WebhookEndpoints::delete($webhook_id));

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
        $url = parent::build_url(WebhookEndpoints::update($webhook_id));

        return parent::$request->patch($url, $data);
    }
    /**
     * Create webhook header
     * 
     * @param array $data: data of header
     * 
     * @return mixed response of the request
     */
    public function create_header(array $data) {
        $url = parent::build_url(WebhookEndpoints::create_header());

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
    public function list_headers(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::build_url(WebhookEndpoints::list_headers());
        $params = parent::build_params($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve header
     * 
     * @param string $header_id: header ID
     * 
     * @return mixed response of the request
     */
    public function retrieve_header(string $header_id) {
        $url = parent::build_url(WebhookEndpoints::retrieve_header($header_id));

        return parent::$request->get($url);
    }
    /**
     * Delete a header
     * 
     * @param string $header_id: header ID
     * 
     * @return mixed response of the request
     */
    public function delete_header(string $header_id) {
        $url = parent::build_url(WebhookEndpoints::delete_header($header_id));

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
    public function list_events(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::build_url(WebhookEndpoints::list_events());
        $params = parent::build_params($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve an event
     * 
     * @param string $event_id: event ID
     * 
     * @return mixed response of the request
     */
    public function retrieve_event(string $event_id) {
        $url = parent::build_url(WebhookEndpoints::retrieve_event($event_id));

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
    public function list_deliveries(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::build_url(WebhookEndpoints::list_deliveries());
        $params = parent::build_params($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Retrieve_delivery
     * 
     * @param string $delivery_id: delivery ID
     * 
     * @return mixed response of the request
     */
    public function retrieve_delivery(string $delivery_id) {
        $url = parent::build_url(WebhookEndpoints::retrieve_delivery($delivery_id));

        return parent::$request->get($url);
    }
    /**
     * Retrieve secret
     * 
     * @return mixed response of the request
     */
    public function retrieve_secret() {
        $url = parent::build_url(WebhookEndpoints::retrieve_secret());

        return parent::$request->get($url);
    }
    /**
     * Retrieve secret after regenerate
     * 
     * @return mixed response of the request
     */
    public function regenerate_secret() {
        $url = parent::build_url(WebhookEndpoints::regenerate_secret());

        return parent::$request->get($url);
    }
}
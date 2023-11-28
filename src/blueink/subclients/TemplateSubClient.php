<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/SubClient.php';
require_once __DIR__ . "//../Endpoints.php";
require_once __DIR__ . "/../helpers/Helper.php";
class TemplateSubClient extends SubClient {
    # TODO paged list function for templates
    /**
     * func description here
     * 
     * params here
     * 
     * @return mixed paginated object
     */
    public function paged_list() {

        return ;
    }
    /**
     * Return a list of template
     * 
     * @param ?int $page: current page, default null
     * @param ?int $per_page: number of items per page, default null
     * @param ?array $query_params: the query params, default null
     * 
     * @return mixed list of template
     */
    public function list(?int $page = null, ?int $per_page = null, ?array $query_params = null) {
        $url = parent::build_url(TemplateEndpoints::list());
        $params = parent::build_params($page, $per_page, $query_params);

        return parent::$request->get($url, ['params' => $params]);
    }
    /**
     * Return a singular template by id
     * 
     * @param string $template_id: id of the template
     * 
     * @return mixed response of the request
     */
    public function retrieve(string $template_id) {
        $url = parent::build_url(TemplateEndpoints::retrieve($template_id));

        return parent::$request->get($url);
    }
}

<?php

namespace Blueink\ClientSDK;

class TemplateSubClient extends SubClient
{
    /**
     * Return a Paginated iterator that lazily fetches Template pages.
     */
    public function pagedList(int $page = 1, int $per_page = 50, ?array $query_params = null): Paginated
    {
        $fn = function (array $args) {
            return $this->list($args['page'], $args['per_page'], $args['additional_data']);
        };

        return new Paginated($fn, $page, $per_page, $query_params);
    }

    public function list(?int $page = null, ?int $per_page = null, ?array $query_params = null): NormalizedResponse
    {
        $url = $this->buildURL(TemplateEndpoints::list());
        $params = $this->buildParams($page, $per_page, $query_params);

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieve(string $template_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(TemplateEndpoints::retrieve($template_id)));
    }

    /**
     * Partially update a Template (PATCH). Typically used to update metadata
     * on an existing Template.
     */
    public function update(string $template_id, array $data): NormalizedResponse
    {
        $url = $this->buildURL(TemplateEndpoints::update($template_id));

        return $this->request->patch($url, ['json' => $data]);
    }
}

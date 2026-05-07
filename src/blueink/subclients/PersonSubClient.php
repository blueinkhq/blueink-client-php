<?php
namespace Blueink\ClientSDK;

class PersonSubClient extends SubClient
{
    /**
     * Create a Person (eg. a signer) record.
     */
    public function create(array $data, ?array $additional_data = null): NormalizedResponse
    {
        if (empty($data["name"])) {
            throw new \InvalidArgumentException("A name is required to create a Person");
        }
        if (!is_null($additional_data)) {
            $data = Helper::mergeAdditionalData($data, $additional_data);
        }
        $url = $this->buildURL(PersonEndpoints::create());

        return $this->request->post($url, ['json' => $data]);
    }

    /**
     * Create a Person using a PersonHelper convenience object.
     */
    public function createFromPersonHelper(PersonHelper $person_helper, ?array $additional_data = null): NormalizedResponse
    {
        return $this->create($person_helper->asArray($additional_data));
    }

    /**
     * Return a Paginated iterator that lazily fetches Person pages.
     */
    public function pagedList(int $page = 1, int $per_page = 50, ?array $additional_data = null): Paginated
    {
        $fn = function (array $args) {
            return $this->list($args['page'], $args['per_page'], $args['additional_data']);
        };

        return new Paginated($fn, $page, $per_page, $additional_data);
    }

    public function list(?int $page = null, ?int $per_page = null, ?array $additional_data = null): NormalizedResponse
    {
        $params = $this->buildParams($page, $per_page, $additional_data);
        $url = $this->buildURL(PersonEndpoints::list());

        return $this->request->get($url, ['query' => $params]);
    }

    public function retrieve(string $person_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(PersonEndpoints::retrieve($person_id)));
    }

    /**
     * @param bool $partial Issue a PATCH instead of a PUT.
     */
    public function update(string $person_id, array $data, bool $partial = false): NormalizedResponse
    {
        $url = $this->buildURL(PersonEndpoints::update($person_id));
        $options = ['json' => $data];

        return $partial ? $this->request->patch($url, $options) : $this->request->put($url, $options);
    }

    public function delete(string $person_id): NormalizedResponse
    {
        return $this->request->delete($this->buildURL(PersonEndpoints::delete($person_id)));
    }
}
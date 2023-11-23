<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/SubClient.php';
require_once __DIR__ . "//../Endpoints.php";
require_once __DIR__ . "/../helpers/Helper.php";
class PersonSubClient extends SubClient
{
    /**
     * Create a person (eg. signer) record
     * 
     * @param array $data: an array difinition of a person
     * 
     * @return mixed Person object
     */
    public function create(array $data, ?array $additional_data = null)
    {
        if (isset($data["name"])) {
            throw new \ErrorException("A name is required to create a person");
        }
        if (!is_null($additional_data)) {
            $data = Helper::merge_additional_data($data, $additional_data);
        }
        $url = parent::build_url(PersonEndpoints::create());

        return parent::$request->post($url, $data);
    }
    /**
     * Create a person using PersonHelper convenience object
     * 
     * @param PersonHelper $person_helper: data as PersonHelper type
     * 
     * @return mixed Person object
     */
    public function create_from_person_helper(PersonHelper $person_helper, ?array $additional_data = null)
    {
        return $this->create($person_helper::as_array($additional_data));
    }
    # TODO need to test the paged_list from bundle helper first
    /**
     * Return an iterable object such that you may laizly fetch a number of person
     * 
     * @param ?int $page: start page, default 1
     * @param ?int $per_page: number of items per page, default 50
     * @param ?array $additional_data: additional data
     * 
     * @return mixed Paginated Iterator object
     */
    public function paged_list(?int $page = 1, ?int $per_page = 50, ?array $additional_data = null)
    {

        return;
    }
    /**
     * Return a list of persons (signer)
     * 
     * @param ?int $page: start page, default null
     * @param ?int $per_page: number of items per page, default null
     * @param ?array $additional_data: additional data
     * 
     * @return mixed List of persons object
     */
    public function list(?int $page = null, ?int $per_page = null, ?array $additional_data = null)
    {
        $params = [];
        if (!is_null($per_page)) {
            $params['per_page'] = $per_page;
        }
        if (!is_null($page)) {
            $params['page'] = $page;
        }

        if (!is_null($additional_data)) {
            $params = Helper::merge_additional_data($params, $additional_data);
        }

        $url = parent::build_url(PersonEndpoints::list());
        $response = parent::$request->get($url, ['params' => $params]);

        return $response;
    }
    /**
     * Retrieve details on a singular person
     * 
     * @param string $person_id: identifying which person to retrieve
     * 
     * @return mixed an person object
     */
    public function retrieve(string $person_id)
    {
        $url = parent::build_url(PersonEndpoints::retrieve($person_id));

        return parent::$request->get($url);
    }
    /**
     * Update a Person record
     * 
     * @param string $person_id: identifying which person to update
     * @param array $data: an [key => value] representation of person's data
     * @param bool $partial: whether to do a partial update, default false
     * 
     * @return mixed an person object
     */
    public function update(string $person_id, array $data, ?bool $partial = false)
    {
        $url = parent::build_url(PersonEndpoints::update($person_id));
        if ($partial) {
            $response = parent::$request->patch($url, $data);
        } else {
            $response = parent::$request->put($url, $data);
        }

        return $response;
    }
    /**
     * Delete a person
     * 
     * @param string $person_id: identifying which person to delete
     * 
     * @return mixed # NOTE description for return here
     */
    public function delete(string $person_id) {
        $url = parent::build_url(PersonEndpoints::delete($person_id));

        return parent::$request->delete($url);
    }
}
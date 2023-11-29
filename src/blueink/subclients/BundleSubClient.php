<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/SubClient.php';
require_once __DIR__ . "//../Endpoints.php";
require_once __DIR__ . "/../helpers/Helper.php";
require_once __DIR__ . "/../helpers/BundleHelper.php";

class BundleSubClient extends SubClient
{
	# TODO prepare file function
	/**
	 * prepare file before create bundle
	 * 
	 */
	private function prepareFiles(mixed $files)
	{

		return [];
	}
	/**
	 * Need some description here
	 * 
	 * @param array $data: bundle data
	 * @param ?array $file: array of file
	 * 
	 * @return mixed
	 */
	public function create(array $data = null, ?array $file = null)
	{
		if (is_null($data)) {
			throw new \ErrorException("Data is required");
		}

		$url = parent::buildURL(BundleEndpoints::create());
		if (is_null($file)) {
			$response = parent::$request->post($url, $data);
		} else {
			$files_data = $this->prepareFiles($file);
			if (is_null($files_data)) {
				throw new \ErrorException('No valid file data provided');
			}

			$response = parent::$request->post($url, ['body' => $data, 'files' => $files_data]);
		}

		return $response;
	}
	/**
	 * Post a Bundle to Blueink application
	 * 
	 * Provided as a convenience to simplify posting of a Bundle. This is the recommended way to create a Bundle
	 * 
	 * @param array $data: data that has been configured as desired.
	 * 
	 * @return mixed Bundle object
	 */
	public function createFromBundleHelper(array $data) 
	{
		return $this->create(['body' => BundleHelper::asData($data)]);
	}
	# TODO paginated function
	/**
	 * An iterable object such that you may lazily fetch a number of Bundles
	 * 
	 * @param ?int $page: current page, default 1
	 * @param ?int $per_page: number of items per page, default 50
	 * @param ?bool $related_data: default false
	 * @param ?array $query_param: query params, default null
	 * 
	 * @return mixed Paginated object
	 */
	public function pagedList(?int $page = 1, ?int $per_page = 50, ?bool $related_data = false, ?array $query_params = null) {
		
		return ;
	}
	/**
	 * retrieve list of bundles
	 * 
	 * @param ?int $page: current page
	 * @param ?int $per_page: number retrieve items per page
	 * @param ?bool $related_data: related data
	 * @param ?array $additional_data: additional data
	 * 
	 * @return mixed response of request
	 */
	public function list(?int $page = null, ?int $per_page = null, ?bool $related_data = false, ?array $additional_data = null)
	{	
		$params = parent::buildParams($page, $per_page, $additional_data);

		# NOTE need to check again this logic
		$params = [
			'data' => $related_data,
			'params' => $params
		];
		$url = parent::buildURL(BundleEndpoints::list());
		$response = parent::$request->get($url, ['params' => $params]);

		return $response;
	}
	/**
	 * retrieve bundle data
	 * 
	 * @param string $bundle_id bundle id 
	 * @param bool $related_data related data, default false
	 * 
	 * @return mixed response of request
	 */
	public function retrieve(string $bundle_id, bool $related_data = false)
	{
		$url = parent::buildURL(BundleEndpoints::retrieve($bundle_id));
		$response = parent::$request->get($url, ['params' => $related_data]);

		return $response;
	}
	/**
	 * cancel bundle
	 * 
	 * @param string $bundle_id: denotes which bundle to cancel
	 * 
	 * @return mixed response of request
	 */
	public function cancel(string $bundle_id) {
		$url = parent::buildURL(BundleEndpoints::cancel($bundle_id));
		
		return parent::$request->put($url);
	}
	/**
	 * List of events for supplied bundle corresponding to the id
	 * 
	 * @param string $bundle_id: which bundle to return events for
	 * 
	 * @return mixed
	 */
	private function listEvents(string $bundle_id) {
		$url = parent::buildURL(BundleEndpoints::listEvents($bundle_id));

		return parent::$request->get($url);
	}
	/**
	 * List of files for the supplied bundle corresponding to the id
	 * 
	 * @param string $bundle_id: which bundle to return files
	 * 
	 * @return mixed
	 */
	private function listFiles(string $bundle_id) {
		$url = parent::buildURL(BundleEndpoints::listFiles($bundle_id));

		return parent::$request->get($url);
	}
	/**
	 * A data for the supplied bundle corresponding to the id
	 * 
	 * @param string $bundle_id: which bundle to return data for
	 * 
	 * @return mixed
	 */
	private function listData(string $bundle_id) {
		$url = parent::buildURL(BundleEndpoints::listData($bundle_id));

		return parent::$request->get($url);
	}
	# TODO need more manual test
	/**
	 * add additional data
	 * 
	 * @param array $bundle: bundle
	 * 
	 * @return void
	 */
	public function _attachAdditionalData(array $bundle) {
		if (is_array($bundle) && isset($bundle['id'])) {
			$bundle_id = $bundle['id'];
		}
		
		$events_reponse = $this->listEvents($bundle_id);
		if ($events_reponse['status'] == 200) {
			$bundle['events'] = $events_reponse['data'];
		}
		if ($bundle['status'] == BUNDLE_STATUS['COMPLETE']) {
			$file_response = $this->listFiles($bundle_id);
			$bundle['$files'] = $file_response['data'];
			$data_response = $this->listData($bundle_id);
			$bundle['data'] = $data_response['data'];
		}
	}
}

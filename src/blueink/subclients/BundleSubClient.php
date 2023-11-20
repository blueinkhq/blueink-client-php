<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/SubClient.php';
require_once __DIR__ . "//../Endpoints.php";
require_once __DIR__ . "/../helpers/Helper.php";

class BundleSubClient extends SubClient
{
	/**
	 * prepare file before create bundle
	 * 
	 */
	private function prepare_files(mixed $files)
	{

		return [];
	}
	/**
	 * Need some description here
	 * 
	 * @param Bundle $data: bundle data
	 * @param ?array $file: array of file
	 * 
	 * @return mixed
	 */
	public function create(Bundle $data = null, ?array $file = null)
	{
		if (is_null($data)) {
			throw new \ErrorException("Data is required");
		}

		$url = parent::build_url(BundleEndpoints::create());
		if (is_null($file)) {
			$response = parent::$request->post($url, ['body' => $data, 'content_type' => 'application/json']);
		} else {
			$files_data = $this->prepare_files($file);
			if (is_null($files_data)) {
				throw new \ErrorException('No valid file data provided');
			}

			$response = parent::$request->post($url, ['body' => $data, 'files' => $files_data]);
		}

		return $response;
	}
	/**
	 * create bundle from bundle helper
	 */
	public function create_from_bundle_helper()
	{

		return '';
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

		$additional_data = [
			'data' => $related_data,
			'params' => $params
		];
		$url = parent::build_url(BundleEndpoints::list());
		$response = parent::$request->get($url, $additional_data);

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
		$url = parent::build_url(BundleEndpoints::retrieve($bundle_id));
		$response = parent::$request->get($url, ['data' => $related_data]);

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
		$url = parent::build_url(BundleEndpoints::cancel($bundle_id));
		
		return parent::$request->put($url);
	}
	/**
	 * List of events for supplied bundle corresponding to the id
	 * 
	 * @param string $bundle_id: which bundle to return events for
	 * 
	 * @return mixed
	 */
	public function list_events(string $bundle_id) {
		$url = parent::build_url(BundleEndpoints::list_events($bundle_id));

		return parent::$request->get($url);
	}
}

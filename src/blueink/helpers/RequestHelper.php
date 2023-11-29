<?php
namespace Blueink\ClientSDK;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/Helper.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use GuzzleHttp\Exception\RequestException;
use ErrorException;

class Pagination
{
	public int $page_number;
	public int $total_pages;
	public int $per_page;
	public int $total_results;
	/**
	 * Pagination fields parsed out for Blueink paged response from 'X-Blueink-Pagination'
	 * 
	 * @param string $pagination_header: string of pagination
	 * 
	 * @return void
	 */
	public function __construct(string $pagination_header)
	{
		$header_array = explode(',', $pagination_header);
		$this->page_number = (int) $header_array[0];
		$this->total_pages = (int) $header_array[1];
		$this->per_page = (int) $header_array[2];
		$this->total_results = (int) $header_array[3];
	}
	public function paginationAsString()
	{
		return "page_number: " . $this->page_number . ", per_page:" . $this->per_page
			. ", total_pages:" . $this->total_pages . ", total_results: " . $this->total_results;
	}
}

class RequestHelper
{
	protected string $private_api_key;
	protected bool $raise_exceptions;
	public function __construct(string $private_api_key, bool $raise_exceptions = true)
	{
		$this->private_api_key = $private_api_key ?? throw new ErrorException('Private api key must be provided');
		$this->raise_exceptions = $raise_exceptions;
	}
	/**
	 * GET request
	 * 
	 * @param string $url: url path
	 * @param ?array [key => value] with fixed key you can have
	 * such as data, json, files, params, headers and content_type
	 * 
	 * @return mixed response of request
	 */
	public function get(string $url, ?array $additional_data = null)
	{
		return $this->makeRequest('GET', $url, $additional_data);
	}
	/**
	 * POST request
	 * 
	 * @param string url path
	 * @param ?array [key => value] with fixed key you can have
	 * such as data, json, files, params, headers and content_type
	 * 
	 * @return mixed response of request
	 */
	public function post(string $url, ?array $additional_data = null)
	{
		return $this->makeRequest('POST', $url, $additional_data);
	}
	/**
	 * PUT request
	 * 
	 * @param string url path
	 * @param ?array [key => value] with fixed key you can have
	 * such as data, json, files, params, headers and content_type
	 * 
	 * @return mixed response of request
	 */
	public function put(string $url, ?array $additional_data = null)
	{
		return $this->makeRequest('PUT', $url, $additional_data);
	}
	/**
	 * PATCH request
	 * 
	 * @param string url path
	 * @param ?array [key => value] with fixed key you can have
	 * such as data, json, files, params, headers and content_type
	 * 
	 * @return mixed response of request
	 */
	public function patch(string $url, ?array $additional_data = null)
	{
		return $this->makeRequest('PATCH', $url, $additional_data);
	}
	/**
	 * DELETE request
	 * 
	 * @param string url path
	 * @param ?array [key => value] with fixed key you can have
	 * such as data, json, files, params, headers and content_type
	 * 
	 * @return mixed response of request
	 */
	public function delete(string $url, ?array $additional_data = null)
	{
		return $this->makeRequest('DELETE', $url, $additional_data);
	}
	/**
	 * making request from method, url and data
	 * 
	 * @param string method of request
	 * @param string url of request
	 * @param ?array [key => value] with fixed key you can have
	 * such as body for object and array type, json, files, params, headers and content_type
	 * 
	 * @return mixed response of the request
	 */
	private function makeRequest(string $method, string $url, ?array $additional_params = [])
	{
		$params = $additional_params['params'] ?? null;
		$data = $additional_params['data'] ?? null;
		# If params of body is json
		$body = $additional_params['json'] ?? null;
		$content_type = $additional_params['content_type'] ?? 'application/json';
		# If params body is object && $body is null
		if (is_null($body)) {
			$body = $additional_params['body'] ?? null;
			if (!is_array($body)) {
				$body = Helper::removeNullProperties($body) ?? null;
			}
			
			$body = json_encode($body);
		}
		
		$headers = $additional_params['headers'] ?? null;
		$headers = $this->buildHeader($content_type, $headers);

		$http = new Client([
			'headers' => $headers,
			'query' => $params,
			'data' => $data,
			'body' => $body,
		]);
		# NOTE 
		# The user need to try catch and handle exception themselves following Guzzle Exception
		$res = $http->request($method, $url);
		#remove --- this block must be remove before commit
		$res_body = $res->getBody();
		$json = json_decode($res_body, true);
		echo json_encode($json, JSON_PRETTY_PRINT);
		echo "\n === $method === \n";
		# --- #
		return json_decode($res->getBody(), true);
	}
	/**
	 * building request header
	 * 
	 * @param string $content_type
	 * @param array $more_headers [key => value] of header
	 * 
	 * @return array $content_type
	 */
	private function buildHeader(?string $content_type = null, ?array $more_headers = null)
	{
		if (is_null($this->private_api_key)) {
			throw new ErrorException('Private API key must be supplied');
		}

		$headers['Authorization'] = TOKEN . $this->private_api_key;

		if (!is_null($content_type))
			$headers['Content-Type'] = $content_type;
		if (!is_null($more_headers)) {
			$headers = Helper::mergeAdditionalData($headers, $more_headers);
		}

		return $headers;
	}
	# TODO getLastResponse should support last response 
	
}

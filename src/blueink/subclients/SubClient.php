<?php
namespace Blueink\ClientSDK;

class SubClient
{
	public string $base_url;
	public static RequestHelper $request;
	/**
	 * __construct SubClient::class
	 * 
	 * @param string base_url
	 * @param RequestHelper request helper
	 * 
	 * @return void
	 */
	public function __construct(string $base_url, RequestHelper $request)
	{
		$this->base_url = $base_url;
		self::$request = $request;
	}
	/** 
	 * get the request
	 * 
	 * @return RequestHelper Request Helper object
	 */
	public function getRequest()
	{
		return $this->request;
	}
	/**
	 * building params
	 * 
	 * @param ?int $page: page
	 * @param ?int $per_page: per_page
	 * @param ?array $additional_params: additional params as [key => value]
	 * 
	 * @return array array of query params
	 */
	public static function buildParams(?int $page = null, ?int $per_page = null, ?array $additional_params = null)
	{
		$params = $additional_params;

		if (is_null($page)) {
			$params["page"] = $page;
		}

		if (is_null($per_page)) {
			$params["per_page"] = $per_page;
		}

		return $params;
	}
	/**
	 * building the request URL
	 * 
	 * @param ?string endpoint
	 * @param ?array additional_data
	 * 
	 * @return string url
	 */
	public static function buildURL(string $endpoint, ?array $additional_data = null)
	{
		if (is_null($additional_data)) {
			return DEFAULT_BASE_URL.$endpoint;
		}

		$params = http_build_query($additional_data);

		return DEFAULT_BASE_URL.$endpoint . "?" . $params;
	}
}

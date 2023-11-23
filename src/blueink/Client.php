<?php
namespace Blueink\ClientSDK;
require_once __DIR__ ."/constants.php";
require_once __DIR__ ."/helpers/RequestHelper.php";
require_once __DIR__ ."/subclients/BundleSubClient.php";
require_once __DIR__ ."/subclients/PersonSubClient.php";
use ErrorException;

class Client
{
	private $private_api_key;
	private $base_url;
	private RequestHelper $request_helper;
	public BundleSubClient $bundles;
	public PersonSubClient $persons;
	/**
	 * Need some description here following guzzle
	 */
	function __construct(string $private_api_key = null, ?string $base_url = null)
	{
		$this->private_api_key = $private_api_key;
		$this->base_url = $base_url;
		
		# if private_api_key is null, the .env file should have BLUEINK_PRIVATE_API_KEY
		if (is_null($this->private_api_key)) {
			$this->private_api_key = getenv("BLUEINK_PRIVATE_API_KEY");
		}

		if (is_null($this->private_api_key)) {
			throw new ErrorException("A Blueink Private API Key must be provided on Client initialization 
			or specified via the environment variable");
		}
		
		# if not using the default base url, the .env file should have BLUEINK_BASE_URL
		if (is_null($this->base_url)) {
			$this->base_url = getenv("BLUEINK_API_URL");
		}

		if (is_null($this->base_url)) {
			$this->base_url = DEFAULT_BASE_URL;
		}

		# create bundle helper
		$this->request_helper = new RequestHelper($this->private_api_key);	
		$this->bundles = new BundleSubClient($this->base_url, $this->request_helper);
		$this->persons = new PersonSubClient($this->base_url, $this->request_helper);
	}
}

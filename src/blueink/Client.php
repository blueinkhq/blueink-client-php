<?php
namespace Blueink\ClientSDK;
require_once __DIR__ ."/constants.php";
require_once __DIR__ ."/helpers/RequestHelper.php";
require_once __DIR__ ."/subclients/BundleSubClient.php";
require_once __DIR__ ."/subclients/PersonSubClient.php";
require_once __DIR__ ."/subclients/PacketSubClient.php";
require_once __DIR__ ."/subclients/TemplateSubClient.php";
require_once __DIR__ ."/subclients/WebhookSubClient.php";
use ErrorException;
class Client
{
	private $private_api_key;
	private $base_url;
	private RequestHelper $request_helper;
	public BundleSubClient $bundles;
	public PersonSubClient $persons;
	public PacketSubClient $packets;
	public TemplateSubClient $templates;
	public WebhookSubClient $webhooks;
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

		# create helper
		$this->request_helper = new RequestHelper($this->private_api_key);	
		$this->bundles = new BundleSubClient($this->base_url, $this->request_helper);
		$this->persons = new PersonSubClient($this->base_url, $this->request_helper);
		$this->packets = new PacketSubClient($this->private_api_key, $this->request_helper);
		$this->templates = new TemplateSubClient($this->private_api_key, $this->request_helper);
		$this->webhooks = new WebhookSubClient($this->private_api_key, $this->request_helper);
	}
}

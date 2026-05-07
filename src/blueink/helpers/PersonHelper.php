<?php
# This is for Person Helper
# Need more description
# Need to development and refactor following BundleHelper
namespace Blueink\ClientSDK;

class PersonHelper
{
	public ?string $name;
	public ?array $metadata;
	public ?array $phones;
	public ?array $emails;
	public function __construct(?array $params = null)
	{
		$params = $params ?? [];
		$this->name = $params["name"] ?? null;
		$this->metadata = $params["metadata"] ?? null;
		$this->phones = $params["phones"] ?? [];
		$this->emails = $params["emails"] ?? [];
	}
	/**
	 * Add phone
	 * 
	 * @param string $phone: phone number
	 * 
	 * @return void
	 */
	public function addPhone(string $phone)
	{
		$this->phones[] = $phone;
	}
	/**
	 * Set phone 
	 * 
	 * @param array $phones: array of string
	 * 
	 * @return void
	 */
	public function setPhones(array $phones)
	{
		$this->phones = $phones;
	}
	/**
	 * Get phones
	 * 
	 * @return array array of string
	 */
	public function getPhones()
	{
		return $this->phones;
	}
	/**
	 * Add email
	 * 
	 * @param string $email: email address
	 * 
	 * @return void
	 */
	public function addEmail(string $email)
	{
		$this->emails[] = $email;
	}
	/**
	 * Set emails
	 * 
	 * @param array $email: array of string
	 * 
	 * @return void
	 */
	public function setEmails(array $emails)
	{
		$this->emails = $emails;
	}
	/**
	 * Get emails
	 * 
	 * @return array array of string
	 */
	public function getEmails()
	{
		return $this->emails;
	}
	/**
	 * Set metadata
	 * 
	 * @param array $metadata: array of metadata
	 * 
	 * @return void
	 */
	public function setMetadata(array $metadata)
	{
		$this->metadata = $metadata;
	}
	/**
	 * Set the Person's display name. Provided in both PHP-style camelCase and
	 * the snake_case form historically exposed by this SDK.
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function set_name(string $name): void
	{
		$this->setName($name);
	}

	/**
	 * Serialize this PersonHelper to the array shape accepted by
	 * PersonSubClient::create(), merging in optional $additional_data.
	 */
	public function asArray(?array $additional_data = null): array
	{
		$channels = [];
		foreach ($this->emails ?? [] as $email) {
			$channels[] = ["email" => $email, "kind" => "em"];
		}
		foreach ($this->phones ?? [] as $phone) {
			$channels[] = ["phone" => $phone, "kind" => "mp"];
		}

		$data = [
			"name" => $this->name,
			"metadata" => $this->metadata,
			"channels" => $channels,
		];

		if (!is_null($additional_data)) {
			$data = Helper::mergeAdditionalData($data, $additional_data);
		}

		return $data;
	}
}

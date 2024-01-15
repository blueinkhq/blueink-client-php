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
	public function __contruct(?array $params = null)
	{
		$this->name = $params["name"] ?? null;
		$this->metadata = $params["metadata"] ?? null;
		$this->phones = $params["phones"] ?? null;
		$this->emails = $params["emails"] ?? null;
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
	 * Set name
	 * 
	 * @param string $name
	 * 
	 * @return void
	 */
	public function set_name(string $name)
	{
		$this->name = $name;
	}
	/**
	 * Convert PersonHelper to array with additional data
	 * 
	 * @param array $data: personal helper data
	 * @param ?array $additional_data
	 * 
	 * @return array array of person helper as [key => value]
	 */
	public function asArray(?array $additional_data = null)
	{
		$channels = array();
		foreach (self::$emails as $email) {  
			$channels[] = ["email" => $email, "kind" => "em"];
		}
		foreach (self::$phones as $phone) {
			$channels[] = ["phone" => $phone, "kind" => "mp"];
		}

		$data = [
			"name" => self::$name,
			"metadata" => self::$metadata,
			"channels" => $channels,
		];

		if (!is_null($additional_data)) {
			$data = Helper::mergeAdditionalData($data, $additional_data);
		}

		return $data;
	}
}

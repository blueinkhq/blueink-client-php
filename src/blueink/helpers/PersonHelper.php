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
	# TODO func description
	public function add_phone(string $phone)
	{
		$this->phones[] = $phone;
	}
	# TODO func description
	public function set_phones(array $phone)
	{
		$this->phones = $phone;
	}
	# TODO func description
	public function get_phones()
	{
		return $this->phones;
	}
	# TODO func description
	public function add_email(string $email)
	{
		$this->emails[] = $email;
	}
	# TODO func description
	public function set_emails(array $emails)
	{
		$this->emails = $emails;
	}
	# TODO func description
	public function get_emails()
	{
		return $this->emails;
	}
	# TODO func description
	public function set_metadata(array $metadata)
	{
		$this->metadata = $metadata;
	}
	# TODO func description
	public function set_name(string $name)
	{
		$this->name = $name;
	}
	# TODO func description
	public static function as_array(array $additional_data)
	{
		$channels = array();
		foreach (self::$emails as $email) { 
			$channels[] = ["email" => $email, "kind" => "em"];
		}
		foreach (self::$phones as $phone) {
			$channels[] = ["phone" => $phone, "kind" => "mp"];
		}

		$person_out = [
			"name" => self::$name,
			"metadata" => self::$metadata,
			"channels" => $channels,
		];

		$data = Helper::merge_additional_data($person_out, $additional_data);

		return $data;
	}
}

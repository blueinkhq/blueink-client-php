<?php
# This is for Person Helper
# Need more description
# Need to development and refactor following BundleHelper
namespace Blueink\ClientSDK;

class PersonHelper
{
	protected $name;
	protected $metadata;
	protected $phones;
	protected $emails;
	public function __contruct($name, $metadata, $phones, $emails)
	{
		$this->name = $name;
		$this->metadata = $metadata;
		$this->phones = $phones;
		$this->emails = $emails;
	}
	public function add_phone($phone)
	{
		$this->phones[] = $phone;
	}
	public function set_phones($phone)
	{
		$this->phones = $phone;
	}
	public function get_phones()
	{
		return $this->phones;
	}
	public function add_email($email)
	{
		$this->emails[] = $email;
	}
	public function set_emails($emails)
	{
		$this->emails = $emails;
	}
	public function get_emails()
	{
		return $this->emails;
	}
	public function set_metadata($metadata)
	{
		$this->metadata = $metadata;
	}
	public function set_name($name)
	{
		$this->name = $name;
	}
	public function as_dict($additional_data)
	{
		$channels = array();
		foreach ($this->emails as $email) {
			$channels[] = ["email" => $email, "kind" => "em"];
		}
		foreach ($this->phones as $phone) {
			$channels[] = ["phone" => $phone, "kind" => "mp"];
		}

		$person_out = [
			"name" => $this->name,
			"metadata" => $this->metadata,
			"channels" => $channels,
		];

		$out_dict[] = $person_out;
		$out_dict[] = $additional_data;

		return $out_dict;
	}
}

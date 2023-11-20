<?php
namespace Blueink\ClientSDK;
require_once __DIR__ ."/../helpers/Helper.php";
use ErrorException;
class ValidationError
{
	# Thinking about should do the validation error and handle error using the Guzzle Exception / Error
}
class Field
{
	public $kind;
	public $key;
	public $x;
	public $y;
	public $w;
	public $h;
	public $label;
	public $page;
	public $v_pattern;
	public $v_min;
	public $v_max;
	public $editors;
	/**
	 * Need some description here
	 */
	public function __construct(array $params = [])
	{
		$this->kind = $params["kind"];
		$this->key = $params["key"];
		$this->x = $params["x"];
		$this->y = $params["y"];
		$this->w = $params["w"];
		$this->h = $params["h"];
		$this->label = ($params["label"]) ? $params["label"] : null;
		$this->page = ($params["page"]) ? $params["page"] : null;
		$this->v_pattern = ($params["v_pattern"]) ? $params["v_pattern"] : null;
		$this->v_min = ($params["v_min"]) ? $params["v_min"] : null;
		$this->v_max = ($params["v_max"]) ? $params["v_max"] : null;
		$this->editors = ($params["editors"]) ? $params["editors"] : null;
	}
	/**
	 * Need some description here
	 */
	static function create($x, $y, $w, $h, $page, $kind, $key = null, $additional_data = null)
	{
		if (!$key) {
			$key = Helper::generate_key("field", 5);
		}
		$params = array(
			"key" => $key,
			"x" => $x,
			"y" => $y,
			"w" => $w,
			"h" => $h,
			"page" => $page,
			"kind" => $kind
		);

		$params = Helper::merge_additional_data($params, $additional_data);
		$obj = new Field($params);

		return $obj;
	}
	/**
	 * Need some description here
	 */
	function kind_is_allowed($v)
	{
		if (!in_array($v, FIELD_KIND)) {
			return "Field Kind $v not allowed.";
		}

		return $v;
	}
	/**
	 * Need some description here
	 */
	function add_editor($editor)
	{
		if ($this->editors == null) {
			$this->editors = array();
		}
		$this->editors[] = $editor;
	}
}
class Packet
{
	public string $key;
	public ?string $name;
	public ?string $email;
	public ?string $phone;
	public ?bool $auth_sms;
	public ?bool $auth_selfie;
	public ?bool $auth_id;
	public ?string $deliver_via;
	public ?string $person_id;
	public ?int $order;
	/**
	 * __construct Packets::class
	 * parameter should be key => value array with the following key and value bellow
	 * E.g: ['key' => string, 'name' => string]
	 * Params[]
	 * required key => string
	 * optional name => string
	 * optional email => string
	 * optional phone => string
	 * optional auth_sms => bool
	 * optional auth_selfie => bool
	 * optional auth_id => bool
	 * optional deliver_via => string
	 * optional person_id => string
	 * optional order => int
	 */
	public function __construct(array $params = [])
	{
		$this->key = $params["key"] ?? throw new ErrorException('__construct Packets::class required key');
		$this->name = $params["name"] ?? null;
		$this->email = $params["email"] ?? null;
		$this->phone = $params["phone"] ?? null;
		$this->auth_sms = $params["auth_sms"] ?? null;
		$this->auth_selfie = $params["selfie"] ?? null;
		$this->auth_id = $params["auth_id"] ?? null;
		$this->deliver_via = $params["deliver_via"] ?? null;
		$this->person_id = $params["person_id"] ?? null;
		$this->order = $params["order"] ?? null;
	}
	/**
	 * 
	 * check deliver_via is allow or not
	 * 
	 * @param string $v
	 * 
	 * @return string 
	 */
	function deliver_via_is_allowed(string $v)
	{
		if (is_null($v) && !in_array($v, DELIVER_VIA)) {
			return "devliver_via $v not allowed.";
		}

		return $v;
	}
	/**
	 * 
	 * create Packet::class
	 * 
	 * @param ?string $key
	 * @param ?string $name
	 * @param array $additional_data
	 * 
	 * @return Packet
	 */
	public static function create(?string $key = null, ?string $name = null, ?array $additional_data = [])
	{
		if (is_null($key)) {
			$key = Helper::generate_key("packet", 5);
		}
		$params = array(
			"key" => $key,
			"name" => $name,
		);
		$params = Helper::merge_additional_data($params, $additional_data);

		$obj = new Packet($params);

		return Helper::remove_null_properties($obj);
	}
}
class TemplateRefFieldValue
{
	public $key;
	public $initial_value;
	/**
	 * Need some description here
	 */
	public function __construct(array $params = [])
	{
		$this->key = $params["key"];
		$this->initial_value = $params["initial_value"];
	}
	/**
	 * Need some description here
	 */
	public static function create($key, $initial_value, $additional_data)
	{
		$params = array(
			"key" => $key,
			"initial_value" => $initial_value,
		);
		$params = Helper::merge_additional_data($params, $additional_data);
		$obj = new TemplateRefFieldValue($params);

		return $obj;
	}
}
class TemplateRef
{
	public $template_id;
	public $assignments;
	public $field_values;
	/**
	 * Need some description here
	 */
	public function __construct(array $params = [])
	{
		$this->template_id = $params["template_id"];
		$this->assignments = $params["assignments"];
		$this->field_values = $params["field_values"];
	}
	/**
	 * Need some description here
	 */
	public static function create($key = null, array $additional_data = [])
	{
		if (is_null($key)) {
			$key = Helper::generate_key('tmpl', 5);
		}
		$params = array('key' => $key);
		$params = Helper::merge_additional_data($params, $additional_data);

		$obj = new TemplateRef($params);

		return $obj;
	}
	/**
	 * Need some description here
	 */
	public function add_assigment($assignment)
	{
		if ($this->assignments == null) {
			$this->assigments = array();
		}
		$this->assignments[] = $assignment;
	}
	/**
	 * Need some description here
	 */
	public function add_field_Value($field_value)
	{
		if ($this->field_values == null) {
			$this->field_values = array();
		}
		$this->field_values[] = $field_value;
	}
}
class Document
{
	public string $key;
	public ?string $file_url;
	public ?string $file_b64;
	public ?int $file_index;
	public ?array $fields;
	public ?bool $parse_tags;
	/**
	 * __construct Document::class
	 * parameter should be key => value array with the following key and value bellow, 
	 * E.g ['key' => string, 'file_url' => string]
	 * 
	 * *** Noted: One of file_url, file_index, file_b64 must be included
	 * required key => string
	 * optional file_url => string
	 * optional file_b64 => string
	 * optional file_index => int
	 * optional fields => array of Field::class
	 * optional parse_tags => bool, default: false
	 */
	public function __construct(array $params = [])
	{
		$this->key = $params['key'] ?? throw new ErrorException("Missing key");
		$this->file_url = $params['file_url'] ?? null;
		$this->file_b64 = $params['file_b64'] ?? null;
		$this->file_index = $params['file_index'] ?? null;
		$this->fields = $params['fields'] ?? null;
		if (isset($params['parse_tags'])) {
			$this->parse_tags = $params['parge_tags'] ?? false;
		}
	}
	/**
	 * 
	 * create Document::class with key
	 * 
	 * @param string $key, defaul: null
	 * @param array  $additional_data, defaul: null
	 * 
	 * @return Document
	 * 
	 */
	public static function create(string $key = null, array $additional_data = [])
	{
		if (is_null($key)) {
			$key = Helper::generate_key('doc', 5);
		}
		$params = ["key" => $key];
		$params = Helper::merge_additional_data($params, $additional_data);
		$obj = new Document($params);
		
		return Helper::remove_null_properties($obj);
	}
	/**
	 * 
	 * add field to document
	 * 
	 * @param Field $field
	 * 
	 * @return void
	 */
	public function add_field(Field $field)
	{
		if (is_null($this->fields)) {
			$this->fields = array();
		}
		$this->fields[] = $field;
	}
	/**
	 * Need some description here
	 */
	public function add_assignment($assignment)
	{
		// ???
	}
}
class Bundle
{
	public array $packets;
	public array $documents;
	public ?string $label;
	public ?string $in_order;
	public ?string $email_subject;
	public ?string $email_message;
	public ?string $sms_message;
	public ?string $requester_email;
	public ?array $cc_emails;
	public ?string $custom_key;
	public ?string $team;
	public ?bool $is_test;
	public ?string $status;
	public ?int $reminder_offset;
	public ?int $reminder_interval;
	public ?string $reminder_expires;
	public ?array $cc_sender;
	/**
	 * __construct Bundle::class
	 * parameter should be key => value array with the following key and value bellow, 
	 * E.g: ['packets' => [Packets::class], 'document' => [Documents::class]]
	 * 
	 * required packets => array of Packets::class
	 * required document => array of Documents:class
	 * optional label => string
	 * optional in_order => string
	 * optional email_subject => string
	 * optional email_message => string
	 * optional sms_message => string
	 * optional requester_email => string <email>
	 * optional cc_emails => array of string <email>
	 * optional custom_key => string
	 * optional team => string
	 * optional status => string
	 * optional reminder_offset => string
	 * optional reminder_interval => string
	 * optional reminder_expires => string
	 * optional cc_sender => string
	 */
	public function __construct(array $params = [])
	{
		$this->packets = $params["packets"] ?? throw new ErrorException("Packet::class is required");
		$this->documents = $params["documents"] ?? throw new ErrorException("Bundle::class is required");
		$this->label = $params["label"] ?? null;
		$this->in_order = $params["in_order"] ?? null;
		$this->email_subject = $params["email_subject"] ?? null;
		$this->email_message = $params["email_message"] ?? null;
		$this->sms_message = $params["sms_message"] ?? null;
		$this->requester_email = $params["requester_email"] ?? null;
		$this->cc_emails = $params["cc_emails"] ?? null;
		$this->custom_key = $params["custom_key"] ?? null;
		$this->team = $params["team"] ?? null;
		$this->is_test = $params["is_test"] ?? null;
		$this->status = $params["status"] ?? null;
		$this->reminder_offset = $params["reminder_offset"] ?? null;
		$this->reminder_interval = $params["reminder_interval"] ?? null;
		$this->reminder_expires = $params["reminder_expires"] ?? null;
		$this->cc_sender = $params["cc_sender"] ?? null;
	}
	/**
	 * Need some description here
	 */
	public static function create(Packet $packets, Document $documents, array $additional_data = [])
	{
		$params = array(
			"packets" => $packets,
			"documents" => $documents,
		);
		$params = Helper::merge_additional_data($params, $additional_data);
		$obj = new Bundle($params);

		return $obj;
	}
	/**
	 * Need some description here
	 */
	public function add_packet($packet)
	{
		if (is_null($this->packets)) {
			$this->packets = array();
		}
		$this->packets[] = $packet;
	}
	/**
	 * Need some description here
	 */
	public function add_document($document)
	{
		if (is_null($this->documents)) {
			$this->documents = array();
		}
		$this->documents[] = $document;
	}
}

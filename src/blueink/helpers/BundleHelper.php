<?php
namespace Blueink\ClientSDK;

class BundleHelper
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
	# NOTE This description should be change follow the Guzzle description for __construct function
	/**
	 * __construct BundleHelper::class
	 * Parameter should be key => value array with the following key and value bellow
	 * E.g: ['label' => string, 'email_subject' => string]
	 * Params[]
	 * optional label => string
	 * optional email_subject => string
	 * optional email_message => string
	 * optional in_order => bool
	 * optional is_test => bool
	 * optional custom_key => string
	 * optional team => string
	 * optional cc_emails => array
	 * optional documents => array of document
	 * optional packets => array of packets
	 * optional file_names => array of string
	 * optional file_types => array of string
	 * optional files => array of string
	 */
	public function __construct(array $params = [])
	{
		$this->label = $params['label'] ?? null;
		$this->email_subject = $params['email_subject'] ?? null;
		$this->email_message = $params['email_message'] ?? null;
		$this->in_order = $params['in_order'] ?? false;
		$this->is_test = $params['is_test'] ?? false;
		$this->custom_key = $params['custom_key'] ?? null;
		$this->team = $params['team'] ?? null;
		$this->cc_emails = $params['cc_emails'] ?? [];
		$this->documents = $params['documents'] ?? [];
		$this->packets = $params['packets'] ?? [];
		# for file uploads
		$this->file_names = $params['file_names'] ?? [];
		$this->file_types = $params['file_types'] ?? [];
		$this->files = $params['files'] ?? [];
	}
	/**
	 * Add cc emails
	 * 
	 * @param string $email
	 * 
	 * @return void
	 */
	public function addCC($email)
	{
		$this->cc_emails[] = $email;
	}
	/**
	 * Add a file using a URL
	 * 
	 * @param string $url: the url
	 * @param array $additional_data: additional data
	 * 
	 * @return string $document->key: return an document key
	 */
	public function addDocumentByURL(string $url = '', array $additional_data = [])
	{
		$document = Document::create(null, array('file_url' => $url));
		$this->documents[$document->key] = $document;

		return $document->key;
	}
	/**
	 * Add file using b64
	 * 
	 * Args: 
	 * 		filename
	 * 		b64str
	 * 		additional_data
	 * 
	 * @return string Document key
	 */
	public function addDocumentByB64(string $filename, string $b64str, array $additional_data = [])
	{
		$document = Document::create(null, array('filename' => $filename, 'file_b64' => $b64str, 'additional_data' => $additional_data));
		$this->documents[$document->key] = $document;

		return $document->key;
	}
	/**
	 * Add file using a file path. File context used, should safely open/close file
	 * 
	 * @param string $file_path: the file path uri 
	 * @param array $additional_data: [key => value] of additional data
	 * 
	 * @return string Document key
	 * 
	 */
	public function addDocumentByPath(string $file_path, array $additional_data = [])
	{
		$file_name = basename($file_path);
		$file_content = file_get_contents($file_path);
		$b64 = base64_encode(utf8_decode($file_content));

		return BundleHelper::addDocumentByB64($file_name, $b64, $additional_data);
	}
	/**
	 * Add a file using a file path. File context used, should safely open/close file
	 * 
	 * @param string $file basically this is file name
	 * @param array	$additional_data [key => value] array of additional_data
	 * 
	 * @return string Document key
	 */
	public function addDocumentByFile(mixed $file, array $additional_data = [])
	{
		$file_name = basename($file);
		$file_content = file_get_contents($file_name);
		$b64 = base64_encode(utf8_decode($file_content));

		return BundleHelper::addDocumentByB64($file_name, $b64, $additional_data);
	}
	/**
	 * Add document by template
	 * 
	 * @param string $template_id: template id
	 * @param array $assignments: array of string
	 * @param array $initial_field_values
	 */
	public function addDocumentTemplate(string $template_id, array $assignments, array $initial_field_values)
	{

	}
	/**
	 * Bundle helper function featch array to object
	 * 
	 * @param array $data: bundle data
	 * 
	 * @return Bundle bundle object
	 */
	public static function asData(array $data)
	{
		$packets = self::$packets;
		$documents = self::$documents;
		$bundle = Bundle::create($packets, $documents, [
			'label' => self::$label,
			'in_order' => self::$in_order,
			'email_subject' => self::$email_subject,
			'email_message' => self::$email_message,
			'sms_message' => self::$sms_message,
			'requester_email' => self::$requester_email,
			'cc_emails' => self::$cc_emails,
			'custom_key' => self::$custom_key,
			'team' => self::$team,
			'is_test' => self::$is_test,
			'status' => self::$status,
			'reminder_offset' => self::$reminder_offset,
			'reminder_interval' => self::$reminder_interval,
			'reminder_expires' => self::$reminder_expires,
			'cc_sender' => self::$cc_sender
		]);

		return $bundle;
	}
}

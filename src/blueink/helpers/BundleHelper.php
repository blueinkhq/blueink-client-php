<?php
namespace Blueink\ClientSDK;

class BundleHelper
{
	public array $packets;
	public array $documents;
	public ?string $label;
	public ?bool $in_order;
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
	public array $file_names;
	public array $file_types;
	public array $files;

	/**
	 * Build a BundleHelper from an associative array of fields.
	 *
	 * Recognized keys (all optional):
	 *   label, email_subject, email_message, sms_message, requester_email,
	 *   in_order (bool), is_test (bool), custom_key, team, cc_emails (array),
	 *   status, reminder_offset, reminder_interval, reminder_expires,
	 *   cc_sender (array), documents (array), packets (array),
	 *   file_names, file_types, files (parallel arrays for multipart uploads)
	 */
	public function __construct(array $params = [])
	{
		$this->label             = $params['label'] ?? null;
		$this->email_subject     = $params['email_subject'] ?? null;
		$this->email_message     = $params['email_message'] ?? null;
		$this->sms_message       = $params['sms_message'] ?? null;
		$this->requester_email   = $params['requester_email'] ?? null;
		$this->in_order          = $params['in_order'] ?? false;
		$this->is_test           = $params['is_test'] ?? false;
		$this->custom_key        = $params['custom_key'] ?? null;
		$this->team              = $params['team'] ?? null;
		$this->cc_emails         = $params['cc_emails'] ?? [];
		$this->status            = $params['status'] ?? null;
		$this->reminder_offset   = $params['reminder_offset'] ?? null;
		$this->reminder_interval = $params['reminder_interval'] ?? null;
		$this->reminder_expires  = $params['reminder_expires'] ?? null;
		$this->cc_sender         = $params['cc_sender'] ?? null;
		$this->documents         = $params['documents'] ?? [];
		$this->packets           = $params['packets'] ?? [];
		# for file uploads
		$this->file_names        = $params['file_names'] ?? [];
		$this->file_types        = $params['file_types'] ?? [];
		$this->files             = $params['files'] ?? [];
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
	 * Add a Document referenced by a publicly accessible URL.
	 *
	 * @return string the generated Document key
	 */
	public function addDocumentByURL(string $url, array $additional_data = []): string
	{
		$additional_data['file_url'] = $url;
		$document = Document::create(null, $additional_data);
		$this->documents[$document->key] = $document;

		return $document->key;
	}

	/**
	 * Add a Document by base64-encoded contents.
	 *
	 * @return string the generated Document key
	 */
	public function addDocumentByB64(string $filename, string $b64str, array $additional_data = []): string
	{
		$additional_data['filename'] = $filename;
		$additional_data['file_b64'] = $b64str;
		$document = Document::create(null, $additional_data);
		$this->documents[$document->key] = $document;

		return $document->key;
	}

	/**
	 * Add a Document by reading from a path on disk and base64-encoding it.
	 */
	public function addDocumentByPath(string $file_path, array $additional_data = []): string
	{
		$file_name = basename($file_path);
		$file_content = file_get_contents($file_path);
		$b64 = base64_encode($file_content);

		return $this->addDocumentByB64($file_name, $b64, $additional_data);
	}

	/**
	 * Add a Document by enqueueing the file path for multipart upload at
	 * Bundle creation time. The file is not read until the request is sent.
	 *
	 * @return int the file_index assigned to the Document
	 */
	public function addDocumentByFile(string $file_path, string $content_type = 'application/pdf', array $additional_data = []): int
	{
		$file_index = count($this->files);
		$this->files[] = $file_path;
		$this->file_names[] = basename($file_path);
		$this->file_types[] = $content_type;

		$additional_data['file_index'] = $file_index;
		$document = Document::create(null, $additional_data);
		$this->documents[$document->key] = $document;

		return $file_index;
	}

	/**
	 * Build a Document from an existing Template and add it to the Bundle.
	 */
	public function addDocumentTemplate(string $template_id, array $assignments = [], array $field_values = []): string
	{
		$tmpl = TemplateRef::create(null, [
			'template_id'  => $template_id,
			'assignments'  => $assignments,
			'field_values' => $field_values,
		]);
		$document = Document::create(null, ['template_id' => $template_id]);
		$this->documents[$document->key] = $document;

		return $document->key;
	}

	/**
	 * Serialize this BundleHelper to the array shape accepted by
	 * BundleSubClient::create().
	 */
	public function asData(): array
	{
		$payload = [
			'label'             => $this->label,
			'in_order'          => $this->in_order,
			'email_subject'     => $this->email_subject,
			'email_message'     => $this->email_message,
			'sms_message'       => $this->sms_message,
			'requester_email'   => $this->requester_email,
			'cc_emails'         => $this->cc_emails,
			'custom_key'        => $this->custom_key,
			'team'              => $this->team,
			'is_test'           => $this->is_test,
			'status'            => $this->status,
			'reminder_offset'   => $this->reminder_offset,
			'reminder_interval' => $this->reminder_interval,
			'reminder_expires'  => $this->reminder_expires,
			'cc_sender'         => $this->cc_sender,
			'packets'           => array_values(array_map([self::class, 'objectToArray'], $this->packets)),
			'documents'         => array_values(array_map([self::class, 'objectToArray'], $this->documents)),
		];

		if (!empty($this->files)) {
			$payload['file_names'] = $this->file_names;
			$payload['file_types'] = $this->file_types;
			$payload['files']      = $this->files;
		}

		return array_filter($payload, fn ($v) => !is_null($v) && $v !== []);
	}

	private static function objectToArray(mixed $value): mixed
	{
		if (is_object($value)) {
			return array_filter((array) $value, fn ($v) => !is_null($v));
		}

		return $value;
	}
}

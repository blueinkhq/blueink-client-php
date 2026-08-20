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
    public ?string $signing_brand;
    public ?string $expires;
    public ?bool $is_test;
    public ?string $status;
    public ?int $reminder_offset;
    public ?int $reminder_interval;
    public ?string $reminder_expires;
    public ?array $cc_sender;
    public array $file_names;
    public array $file_types;
    public array $files;
    public ?EnvelopeTemplate $envelope_template;

    /**
     * Build a BundleHelper from an associative array of fields.
     *
     * Recognized keys (all optional):
     *   label, email_subject, email_message, sms_message, requester_email,
     *   in_order (bool), is_test (bool), custom_key, team, signing_brand, expires,
     *   cc_emails (array), status, reminder_offset, reminder_interval,
     *   reminder_expires, cc_sender (array), documents (array), packets (array),
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
        $this->signing_brand     = $params['signing_brand'] ?? null;
        $this->expires           = $params['expires'] ?? null;
        $this->cc_emails         = $params['cc_emails'] ?? [];
        $this->status            = $params['status'] ?? null;
        $this->reminder_offset   = $params['reminder_offset'] ?? null;
        $this->reminder_interval = $params['reminder_interval'] ?? null;
        $this->reminder_expires  = $params['reminder_expires'] ?? null;
        $this->cc_sender         = $params['cc_sender'] ?? null;
        $this->documents         = $params['documents'] ?? [];
        $this->packets           = $params['packets'] ?? [];
        $this->envelope_template = $params['envelope_template'] ?? null;
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
     * Add a Document from an HTML string for HTML-to-PDF conversion.
     *
     * @return string the generated Document key
     */
    public function addDocumentByHTML(string $html_content, array $additional_data = []): string
    {
        $additional_data['file_html'] = $html_content;
        $document = Document::create(null, $additional_data);
        $this->documents[$document->key] = $document;

        return $document->key;
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
     * Add a TemplateRef to the bundle, optionally seeding role assignments and
     * initial field values.
     *
     * @param array<string,string> $assignments        role => signer-key
     * @param array<string,mixed>  $initial_field_values field-key => initial value
     *
     * @return string the generated TemplateRef key
     */
    public function addDocumentTemplate(string $template_id, array $assignments = [], array $initial_field_values = [], array $additional_data = []): string
    {
        $assigns = [];
        foreach ($assignments as $role => $signer) {
            $assigns[] = TemplateRefAssignment::create($role, $signer);
        }

        $vals = [];
        foreach ($initial_field_values as $field_key => $init_val) {
            $vals[] = TemplateRefFieldValue::create($field_key, $init_val);
        }

        $additional_data['template_id']  = $template_id;
        $additional_data['assignments']  = $assigns;
        $additional_data['field_values'] = $vals;

        $template = TemplateRef::create(null, $additional_data);
        $this->documents[$template->key] = $template;

        return $template->key;
    }

    /**
     * Add a field to a Document already attached to this bundle.
     *
     * @param string[] $editors signer (Packet) keys allowed to edit the field
     *
     * @return string the generated Field key
     */
    public function addField(
        string $document_key,
        int $x,
        int $y,
        int $w,
        int $h,
        int $p,
        string $kind,
        ?array $editors = null,
        ?string $label = null,
        ?string $v_pattern = null,
        ?int $v_min = null,
        ?int $v_max = null,
        ?string $v_regex = null,
        ?string $v_regex_msg = null,
        ?array $v_attachment_types = null,
        ?string $key = null,
        array $additional_data = []
    ): string {
        if (!isset($this->documents[$document_key])) {
            throw new \RuntimeException("No document found with key {$document_key}!");
        }

        $additional_data = array_merge($additional_data, array_filter([
            'label'              => $label,
            'v_pattern'          => $v_pattern,
            'v_min'              => $v_min,
            'v_max'              => $v_max,
            'v_regex'            => $v_regex,
            'v_regex_msg'        => $v_regex_msg,
            'v_attachment_types' => $v_attachment_types,
        ], fn ($v) => !is_null($v)));

        $field = Field::create($x, $y, $w, $h, $p, $kind, $key, $additional_data);
        if ($editors) {
            foreach ($editors as $editor_key) {
                $field->addEditor($editor_key);
            }
        }

        $this->documents[$document_key]->addField($field);

        return $field->key;
    }
    /**
     * Add an auto-placement field to a Document. Auto-placements search for a
     * text anchor at runtime and position the field relative to it.
     *
     * @param string[] $editors signer (Packet) keys allowed to edit the field
     */
    public function addAutoPlacement(
        string $document_key,
        string $kind,
        string $search,
        int $w,
        int $h,
        int $offset_x = 0,
        int $offset_y = 0,
        ?array $editors = null,
        ?int $page = null,
        ?array $v_attachment_types = null,
        array $additional_data = []
    ): void {
        if (!isset($this->documents[$document_key])) {
            throw new \RuntimeException("No document found with key {$document_key}!");
        }

        $additional_data = array_merge($additional_data, array_filter([
            'offset_x'           => $offset_x,
            'offset_y'           => $offset_y,
            'page'               => $page,
            'v_attachment_types' => $v_attachment_types,
        ], fn ($v) => !is_null($v)));

        $auto_placement = AutoPlacement::create($kind, $search, $w, $h, $additional_data);
        if ($editors) {
            foreach ($editors as $editor_key) {
                $auto_placement->addEditor($editor_key);
            }
        }

        $this->documents[$document_key]->addAutoPlacement($auto_placement);
    }

    /**
     * Create and add a signer (Packet). Either email or phone is required.
     *
     * @return string the generated Packet key
     */
    public function addSigner(
        ?string $name = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $deliver_via = null,
        ?string $person_id = null,
        bool $auth_sms = false,
        bool $auth_selfie = false,
        bool $auth_id = false,
        ?int $order = null,
        ?string $key = null,
        array $additional_data = []
    ): string {
        if (is_null($email) && is_null($phone)) {
            throw new \InvalidArgumentException('Packet must have either an email or phone number');
        }

        $additional_data = array_merge($additional_data, array_filter([
            'email'       => $email,
            'phone'       => $phone,
            'auth_sms'    => $auth_sms,
            'auth_selfie' => $auth_selfie,
            'auth_id'     => $auth_id,
            'deliver_via' => $deliver_via,
            'person_id'   => $person_id,
            'order'       => $order,
        ], fn ($v) => !is_null($v) && $v !== false));

        $packet = Packet::create($key, $name, $additional_data);
        $this->packets[$packet->key] = $packet;

        return $packet->key;
    }

    /**
     * Assign a signer to a role on a TemplateRef document.
     */
    public function assignRole(string $document_key, string $signer_key, string $role, array $additional_data = []): void
    {
        if (!isset($this->documents[$document_key])) {
            throw new \RuntimeException("No document found with key {$document_key}!");
        }
        if (!($this->documents[$document_key] instanceof TemplateRef)) {
            throw new \RuntimeException("Document found with key {$document_key} is not a Template!");
        }
        if (!isset($this->packets[$signer_key])) {
            throw new \RuntimeException("Signer {$signer_key} does not have a corresponding packet");
        }

        $assignment = TemplateRefAssignment::create($role, $signer_key, $additional_data);
        $this->documents[$document_key]->addAssignment($assignment);
    }

    /**
     * Set an initial value for a field on a TemplateRef document.
     */
    public function setValue(string $document_key, string $key, mixed $value, array $additional_data = []): void
    {
        if (!isset($this->documents[$document_key])) {
            throw new \RuntimeException("No document found with key {$document_key}!");
        }
        if (!($this->documents[$document_key] instanceof TemplateRef)) {
            throw new \RuntimeException("Document found with key {$document_key} is not a Template!");
        }

        $field_val = TemplateRefFieldValue::create($key, $value, $additional_data);
        $this->documents[$document_key]->addFieldValue($field_val);
    }

    /**
     * Configure this bundle to be created from an envelope template. The
     * envelope template carries its own document/signer/field configuration;
     * pair this with addSigner() and asDataForEnvelopeTemplate().
     *
     * @param array<string,mixed>|null $field_values field-key => initial value
     */
    public function setEnvelopeTemplate(string $template_id, ?array $field_values = null, array $additional_data = []): void
    {
        $vals = [];
        if ($field_values) {
            foreach ($field_values as $field_key => $init_val) {
                $vals[] = EnvelopeTemplateFieldValue::create($field_key, $init_val);
            }
        }

        $this->envelope_template = EnvelopeTemplate::create(
            $template_id,
            $vals !== [] ? $vals : null,
            $additional_data
        );
    }

    /**
     * Append a field value to the previously configured envelope template.
     */
    public function addEnvelopeTemplateFieldValue(string $key, mixed $initial_value, array $additional_data = []): void
    {
        if ($this->envelope_template === null) {
            throw new \RuntimeException('No envelope template set. Call setEnvelopeTemplate() first.');
        }

        $field_val = EnvelopeTemplateFieldValue::create($key, $initial_value, $additional_data);
        $this->envelope_template->addFieldValue($field_val);
    }

    /**
     * Serialize this BundleHelper to the array shape accepted by
     * BundleSubClient::create(). Mirrors Python's
     * Bundle.dict(exclude_unset=True, exclude_none=True).
     */
    public function asData(array $additional_data = []): array
    {
        $bundle = $this->compileBundle($additional_data);
        $payload = Helper::modelToArray($bundle);

        if (!empty($this->files)) {
            $payload['file_names'] = $this->file_names;
            $payload['file_types'] = $this->file_types;
            $payload['files']      = $this->files;
        }

        return $payload;
    }

    /**
     * Serialize this BundleHelper for the create-from-envelope-template
     * endpoint. Returns packets + envelope_template plus selected bundle-level
     * metadata.
     */
    public function asDataForEnvelopeTemplate(array $additional_data = []): array
    {
        if ($this->envelope_template === null) {
            throw new \RuntimeException('No envelope template set. Call setEnvelopeTemplate() first.');
        }

        $result = [
            'packets'           => array_values(array_map(
                fn ($p) => Helper::modelToArray($p),
                $this->packets
            )),
            'envelope_template' => Helper::modelToArray($this->envelope_template),
        ];

        foreach ([
            'label'         => $this->label,
            'is_test'       => $this->is_test,
            'email_subject' => $this->email_subject,
            'email_message' => $this->email_message,
            'cc_emails'     => $this->cc_emails,
            'custom_key'    => $this->custom_key,
            'team'          => $this->team,
            'signing_brand' => $this->signing_brand,
            'expires'       => $this->expires,
        ] as $key => $value) {
            if (!is_null($value) && $value !== [] && $value !== false) {
                $result[$key] = $value;
            }
        }

        return array_merge($result, $additional_data);
    }

    private function compileBundle(array $additional_data = []): Bundle
    {
        $packets = array_values($this->packets);
        $documents = array_values($this->documents);

        $bundle_params = array_merge([
            'label'             => $this->label,
            'in_order'          => $this->in_order,
            'email_subject'     => $this->email_subject,
            'email_message'     => $this->email_message,
            'sms_message'       => $this->sms_message,
            'requester_email'   => $this->requester_email,
            'cc_emails'         => $this->cc_emails ?: null,
            'custom_key'        => $this->custom_key,
            'team'              => $this->team,
            'signing_brand'     => $this->signing_brand,
            'expires'           => $this->expires,
            'is_test'           => $this->is_test,
            'status'            => $this->status,
            'reminder_offset'   => $this->reminder_offset,
            'reminder_interval' => $this->reminder_interval,
            'reminder_expires'  => $this->reminder_expires,
            'cc_sender'         => $this->cc_sender,
        ], $additional_data);

        return Bundle::create($packets, $documents, $bundle_params);
    }
}

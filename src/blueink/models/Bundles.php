<?php

namespace Blueink\ClientSDK;

require_once __DIR__ . '/../helpers/Helper.php';
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
    public $v_regex;
    public $v_regex_msg;
    public $v_attachment_types;
    public $editors;
    /**
     * Need some description here
     */
    public function __construct(array $params = [])
    {
        $this->kind = $params['kind'] ?? null;
        $this->key = $params['key'] ?? null;
        $this->x = $params['x'] ?? null;
        $this->y = $params['y'] ?? null;
        $this->w = $params['w'] ?? null;
        $this->h = $params['h'] ?? null;
        $this->label = $params['label'] ?? null;
        $this->page = $params['page'] ?? null;
        $this->v_pattern = $params['v_pattern'] ?? null;
        $this->v_min = $params['v_min'] ?? null;
        $this->v_max = $params['v_max'] ?? null;
        $this->v_regex = $params['v_regex'] ?? null;
        $this->v_regex_msg = $params['v_regex_msg'] ?? null;
        $this->v_attachment_types = $params['v_attachment_types'] ?? null;
        $this->editors = $params['editors'] ?? null;
    }
    /**
     * Need some description here
     */
    public static function create($x, $y, $w, $h, $page, $kind, $key = null, $additional_data = null)
    {
        if (!$key) {
            $key = Helper::generateKey('field', 5);
        }
        $params = [
            'key' => $key,
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h,
            'page' => $page,
            'kind' => $kind,
        ];

        $params = Helper::mergeAdditionalData($params, $additional_data);
        $obj = new Field($params);

        return $obj;
    }
    /**
     * Need some description here
     */
    public function kindIsAllowed($v)
    {
        if (!in_array($v, FIELD_KIND)) {
            return "Field Kind $v not allowed.";
        }

        return $v;
    }
    /**
     * Need some description here
     */
    public function addEditor($editor)
    {
        if ($this->editors == null) {
            $this->editors = [];
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
        $this->key = $params['key'] ?? throw new ErrorException('__construct Packets::class required key');
        $this->name = $params['name'] ?? null;
        $this->email = $params['email'] ?? null;
        $this->phone = $params['phone'] ?? null;
        $this->auth_sms = $params['auth_sms'] ?? null;
        $this->auth_selfie = $params['auth_selfie'] ?? null;
        $this->auth_id = $params['auth_id'] ?? null;
        $this->deliver_via = $params['deliver_via'] ?? null;
        $this->person_id = $params['person_id'] ?? null;
        $this->order = $params['order'] ?? null;
    }
    /**
     *
     * check deliver_via is allow or not
     *
     * @param string $v
     *
     * @return string
     */
    public function deliverViaIsAllowed(string $v)
    {
        if (!in_array($v, DELIVER_VIA)) {
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
    public static function create(?string $key = null, ?string $name = null, ?array $additional_data = null): self
    {
        if (is_null($key)) {
            $key = Helper::generateKey('packet', 5);
        }
        $params = [
            'key' => $key,
            'name' => $name,
        ];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new Packet($params);
    }
}
class AutoPlacement
{
    public ?string $kind;
    public ?string $search;
    public ?int $w;
    public ?int $h;
    public ?int $offset_x;
    public ?int $offset_y;
    public ?int $page;
    public ?array $editors;
    public ?array $v_attachment_types;

    public function __construct(array $params = [])
    {
        $this->kind = $params['kind'] ?? null;
        $this->search = $params['search'] ?? null;
        $this->w = $params['w'] ?? null;
        $this->h = $params['h'] ?? null;
        $this->offset_x = $params['offset_x'] ?? null;
        $this->offset_y = $params['offset_y'] ?? null;
        $this->page = $params['page'] ?? null;
        $this->editors = $params['editors'] ?? null;
        $this->v_attachment_types = $params['v_attachment_types'] ?? null;
    }

    public static function create(string $kind, string $search, int $w, int $h, ?array $additional_data = null): self
    {
        $params = [
            'kind' => $kind,
            'search' => $search,
            'w' => $w,
            'h' => $h,
        ];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new AutoPlacement($params);
    }

    public function addEditor(string $editor): void
    {
        if (is_null($this->editors)) {
            $this->editors = [];
        }
        $this->editors[] = $editor;
    }
}
class EnvelopeTemplateFieldValue
{
    public ?string $key;
    public mixed $initial_value;

    public function __construct(array $params = [])
    {
        $this->key = $params['key'] ?? null;
        $this->initial_value = $params['initial_value'] ?? null;
    }

    public static function create(string $key, mixed $initial_value, ?array $additional_data = null): self
    {
        $params = ['key' => $key, 'initial_value' => $initial_value];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new EnvelopeTemplateFieldValue($params);
    }
}
class EnvelopeTemplate
{
    public ?string $template_id;
    public ?array $field_values;

    public function __construct(array $params = [])
    {
        $this->template_id = $params['template_id'] ?? null;
        $this->field_values = $params['field_values'] ?? null;
    }

    public static function create(string $template_id, ?array $field_values = null, ?array $additional_data = null): self
    {
        $params = ['template_id' => $template_id, 'field_values' => $field_values];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new EnvelopeTemplate($params);
    }

    public function addFieldValue(EnvelopeTemplateFieldValue $field_value): void
    {
        if (is_null($this->field_values)) {
            $this->field_values = [];
        }
        $this->field_values[] = $field_value;
    }
}
class TemplateRefFieldValue
{
    public ?string $key;
    public mixed $initial_value;

    public function __construct(array $params = [])
    {
        $this->key = $params['key'] ?? null;
        $this->initial_value = $params['initial_value'] ?? null;
    }

    public static function create(string $key, mixed $initial_value, ?array $additional_data = null): self
    {
        $params = [
            'key' => $key,
            'initial_value' => $initial_value,
        ];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new TemplateRefFieldValue($params);
    }
}
class TemplateRefAssignment
{
    public ?string $role;
    public ?string $signer;

    public function __construct(array $params = [])
    {
        $this->role = $params['role'] ?? null;
        $this->signer = $params['signer'] ?? null;
    }

    public static function create(string $role, string $signer, ?array $additional_data = null): self
    {
        $params = ['role' => $role, 'signer' => $signer];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new TemplateRefAssignment($params);
    }
}
class TemplateRef
{
    public ?string $key;
    public ?string $template_id;
    public ?array $assignments;
    public ?array $field_values;

    public function __construct(array $params = [])
    {
        $this->key          = $params['key'] ?? null;
        $this->template_id  = $params['template_id'] ?? null;
        $this->assignments  = $params['assignments'] ?? null;
        $this->field_values = $params['field_values'] ?? null;
    }

    public static function create(?string $key = null, ?array $additional_data = null): self
    {
        if (is_null($key)) {
            $key = Helper::generateKey('tmpl', 5);
        }
        $params = ['key' => $key];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new TemplateRef($params);
    }

    public function addAssignment(TemplateRefAssignment $assignment): void
    {
        if ($this->assignments === null) {
            $this->assignments = [];
        }
        $this->assignments[] = $assignment;
    }

    public function addFieldValue(TemplateRefFieldValue $field_value): void
    {
        if ($this->field_values === null) {
            $this->field_values = [];
        }
        $this->field_values[] = $field_value;
    }
}
class Document
{
    public string $key;
    public ?string $file_url;
    public ?string $file_b64;
    public ?string $file_html;
    public ?string $filename;
    public ?int $file_index;
    public ?array $fields;
    public ?array $auto_placements;
    public ?string $html_fields_mode;
    public ?bool $parse_tags;

    public function __construct(array $params = [])
    {
        $this->key              = $params['key'] ?? throw new ErrorException('Missing key');
        $this->file_url         = $params['file_url'] ?? null;
        $this->file_b64         = $params['file_b64'] ?? null;
        $this->file_html        = $params['file_html'] ?? null;
        $this->filename         = $params['filename'] ?? null;
        $this->file_index       = $params['file_index'] ?? null;
        $this->fields           = $params['fields'] ?? null;
        $this->auto_placements  = $params['auto_placements'] ?? null;
        $this->html_fields_mode = $params['html_fields_mode'] ?? null;
        $this->parse_tags       = $params['parse_tags'] ?? null;
    }

    public static function create(?string $key = null, ?array $additional_data = null): self
    {
        if (is_null($key)) {
            $key = Helper::generateKey('doc', 5);
        }
        $params = ['key' => $key];
        $params = Helper::mergeAdditionalData($params, $additional_data);

        return new Document($params);
    }

    public function addField(Field $field): void
    {
        if (is_null($this->fields)) {
            $this->fields = [];
        }
        $this->fields[] = $field;
    }

    public function addAutoPlacement(AutoPlacement $auto_placement): void
    {
        if (is_null($this->auto_placements)) {
            $this->auto_placements = [];
        }
        $this->auto_placements[] = $auto_placement;
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
    public ?string $signing_brand;
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
        $this->packets = $params['packets'] ?? throw new ErrorException('Packet::class is required');
        $this->documents = $params['documents'] ?? throw new ErrorException('Bundle::class is required');
        $this->label = $params['label'] ?? null;
        $this->in_order = $params['in_order'] ?? null;
        $this->email_subject = $params['email_subject'] ?? null;
        $this->email_message = $params['email_message'] ?? null;
        $this->sms_message = $params['sms_message'] ?? null;
        $this->requester_email = $params['requester_email'] ?? null;
        $this->cc_emails = $params['cc_emails'] ?? null;
        $this->custom_key = $params['custom_key'] ?? null;
        $this->team = $params['team'] ?? null;
        $this->is_test = $params['is_test'] ?? null;
        $this->status = $params['status'] ?? null;
        $this->reminder_offset = $params['reminder_offset'] ?? null;
        $this->reminder_interval = $params['reminder_interval'] ?? null;
        $this->reminder_expires = $params['reminder_expires'] ?? null;
        $this->cc_sender = $params['cc_sender'] ?? null;
        $this->signing_brand = $params['signing_brand'] ?? null;
    }
    /**
     * Create Bundle
     *
     * @param array $packets: array of packet object
     * @param array $documents: array of document opbject
     * @param array $additional_data: additional data, default []
     *
     * @return Bundle bundle object
     */
    public static function create(array $packets, array $documents, array $additional_data = [])
    {
        $params = [
            'packets' => $packets,
            'documents' => $documents,
        ];
        $params = Helper::mergeAdditionalData($params, $additional_data);
        $obj = new Bundle($params);

        return $obj;
    }
    /**
     * Add packets to bundle
     *
     * @param Packet $packet: packet object
     *
     * @return void
     */
    public function addPacket(Packet $packet)
    {
        $this->packets[] = $packet;
    }
    /**
     * Add document to bundle
     *
     * @param Document $document: document object
     *
     * @return void
     */
    public function addDocument(Document $document)
    {
        $this->documents[] = $document;
    }
}

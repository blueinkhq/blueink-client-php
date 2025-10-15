<?php
namespace Blueink\ClientSDK;

const BLUEINK_PAGINATION_HEADER = "X-Blueink-Pagination";

const DEFAULT_BASE_URL = "https://api.blueink.com/api/v2";

const ENV_BLUEINK_PRIVATE_API_KEY = "BLUEINK_PRIVATE_KEY";

const ENV_BLUEINK_API_URL = "BLUEINK_API_URL";

const TOKEN = "TOKEN ";

const ATTACHMENT_TYPE = array(
	"JPG" => "jgp",
	"JPEG" => "jpeg",
	"PNG" => "png",
	"GIF" => "gif",
	"SVG" => "svg",
	"PDF" => "pdf",
	"DOC" => "doc",
	"DOCX" => "docx",
	"PPT" => "ppt",
	"PPTX" => "pptx",
	"XLS" => "xls",
	"XLSX" => "xlsx",
	"RTF" => "rtf",
	"TXT" => "txt",
);

const BUNDLE_ORDER = array(
	"CREATED" => "created",
	"SENT" => "sent",
	"COMPLETED_AT" => "compeleted_at",
);

const BUNDLE_STATUS = array(
	"NEW" => "ne",
	"DRAFT" => "dr",
	"PENDING" => "pe",
	"SENT" => "se",
	"STARTED" => "st",
	"CANCELLED" => "ca",
	"EXPIRED" => "ex",
	"COMPLETE" => "co",
	"FAILED" => "fa",
);

const SEND_VIA = array(
	"EMAIL" => "em",
	"SMS" => "sm",
	"KIOSK" => "ki",
	"BOTH" => "bo",
);

const DELIVER_VIA = array(
	"EMAIL" => "email",
	"EMBED" => "embed",
	"SMS" => "phone",
);

const FIELD_KIND = array(
	"ESIGNATURE" => "sig",
	"INITIALS" => "ini",
	"SIGNERNAME" => "snm",
	"SIGNINGDATE" => "sdt",
	"INPUT" => "inp",
	"TEXT" => "txt",
	"DATE" => "dat",
	"CHECKBOX" => "chk",
	"CHECKBOXES" => "cbx",
	"ATTACHMENT" => "att",
);

const PACKET_STATUS = array(
	"NEW" => "ne",
	"READY" => "re",
	"SENT" => "se",
	"STARTED" => "st",
	"CANCELLED" => "ca",
	"EXPIRED" => "ex",
	"COMPLETE" => "co",
	"FAILED" => "fa",
);

const V_PATTERN = array(
	"EMAIL" => "email",
	"BANK_ROUTING" => "bank_routing",
	"BANK_ACCOUNT" => "bank_account",
	"LETTERS" => "letters",
	"NUMBERS" => "numbers",
	"PHONE" => "phone",
	"SSN" => "ssn",
	"ZIP_CODE" => "zip_code",
);

const EVENT_TYPE = array(
	"EVENT_BUNDLE_LAUNCHED" => "bundle_sent",
	"EVENT_BUNDLE_COMPLETE" => "bundle_complete",
	"EVENT_BUNDLE_DOCS_READY" => "bundle_docs_ready",
	"EVENT_BUNDLE_ERROR" => "bundle_error",
	"EVENT_BUNDLE_CANCELLED" => "bundle_cancelled",
	"EVENT_PACKET_VIEWED" => "packet_viewed",
	"EVENT_PACKET_COMPLETE" => "packet_complete",
);

# Blueink API Client for PHP

A PHP SDK for the [Blueink](https://blueink.com) eSignature REST API.
For full API reference see the [Blueink API v2 docs](https://developer.blueink.com).

## Requirements

- PHP 8.1 or newer
- ext-json
- [Guzzle](http://docs.guzzlephp.org/en/stable/) 7.x (installed as a dependency)

## Installation

```bash
composer require blueink/blueink-client-php
```

## Quickstart

```php
use Blueink\ClientSDK\Client;

// Provide the key directly...
$client = new Client('<YOUR_PRIVATE_API_KEY>');

// ...or set BLUEINK_PRIVATE_API_KEY in the environment and call:
// $client = new Client();

$response = $client->bundles->list();
foreach ($response->data as $bundle) {
    echo $bundle['id'] . "\n";
}
```

## Configuration

The `Client` constructor accepts:

| Argument | Default | Description |
| --- | --- | --- |
| `$private_api_key` | `getenv('BLUEINK_PRIVATE_API_KEY')` | Blueink private API key. |
| `$base_url`        | `getenv('BLUEINK_API_URL')` ?? `https://api.blueink.com/api/v2` | Override for sandbox or custom hosts. |
| `$raise_exceptions`| `true` | When `false`, 4XX/5XX responses are returned as `NormalizedResponse` instead of throwing a Guzzle exception. |

```php
$client = new Client(
    private_api_key: 'sk_live_…',
    base_url: 'https://sandbox.blueink.com/api/v2',
    raise_exceptions: false,
);
```

## Responses: `NormalizedResponse`

Every subclient method returns a `Blueink\ClientSDK\NormalizedResponse`:

```php
$response = $client->bundles->retrieve('abc123');

$response->status;           // int  HTTP status code
$response->data;             // mixed Decoded JSON body (associative array) or raw string
$response->headers;          // array<string,string> Response headers (last value per name)
$response->pagination;       // ?Pagination Parsed X-Blueink-Pagination header (list endpoints)
$response->originalResponse; // Psr\Http\Message\ResponseInterface
```

The most-recent response for a given subclient is also retained:

```php
$last = $client->bundles->getLastResponse();
```

## Subclients

The `Client` exposes one property per resource:

| Property | Class | Resource |
| --- | --- | --- |
| `$client->bundles`   | `BundleSubClient`   | Bundles (envelopes) |
| `$client->persons`   | `PersonSubClient`   | Persons (signers / contacts) |
| `$client->packets`   | `PacketSubClient`   | Packets (per-recipient) |
| `$client->templates` | `TemplateSubClient` | Document templates |
| `$client->envelope_templates` | `EnvelopeTemplateSubClient` | Envelope templates |
| `$client->webhooks`  | `WebhookSubClient`  | Webhooks, headers, events, deliveries |

### Bundles

Create a Bundle from a hand-built payload:

```php
$response = $client->bundles->create([
    'label'   => 'A Test Bundle',
    'is_test' => true,
    'packets' => [[
        'key'   => 'signer-1',
        'name'  => 'Peter Gibbons',
        'email' => 'peter.gibbons@example.com',
    ]],
    'documents' => [[
        'key'         => 'doc-01',
        'file_url'    => 'https://example.com/contract.pdf',
        'fields'      => [],
    ]],
]);

$bundle_id = $response->data['id'];
```

Or use `BundleHelper` to assemble it. The helper exposes convenience methods
for adding signers, documents, fields, and auto-placements without hand-rolling
the nested payload:

```php
use Blueink\ClientSDK\BundleHelper;

$bundle = new BundleHelper([
    'label'         => 'A Test Bundle',
    'email_subject' => 'Please sign this test bundle',
    'is_test'       => true,
]);

// Add signers (each requires email or phone). Returns the generated packet key.
$signer1 = $bundle->addSigner(name: 'Peter Gibbons', email: 'peter@example.com');
$signer2 = $bundle->addSigner(name: 'Bill Lumbergh', email: 'bill@example.com');

// Add a Document by URL, base64, file path (read at build time), file path
// (streamed as multipart at request time), or raw HTML.
$doc_key = $bundle->addDocumentByURL('https://example.com/contract.pdf');
// $bundle->addDocumentByPath('/tmp/nda.pdf');
// $bundle->addDocumentByFile('/tmp/large.pdf', 'application/pdf');
// $bundle->addDocumentByHTML('<h1>Statement of Work</h1>...');

// Add a field at fixed coordinates, scoped to a list of editor packet keys.
$bundle->addField(
    document_key: $doc_key,
    x: 1, y: 15, w: 60, h: 6, p: 1,
    kind: 'inp',
    editors: [$signer1, $signer2],
    label: 'Full name',
);

// Or let the API auto-place a field by searching the document text.
$bundle->addAutoPlacement(
    document_key: $doc_key,
    kind: 'sig',
    search: 'Signature:',
    w: 30, h: 6,
    offset_x: 8, offset_y: -1,
    editors: [$signer1],
);

$response = $client->bundles->createFromBundleHelper($bundle);
```

When the helper has files queued via `addDocumentByFile()`, the SDK transparently
switches the request to `multipart/form-data`.

#### Building a Bundle from a document Template

`addDocumentTemplate()` adds a Document backed by an existing template, with
optional role assignments and initial field values. `assignRole()` and
`setValue()` append to the same Document after the fact:

```php
$signer = $bundle->addSigner(
    name: 'Peter Gibbons',
    email: 'peter@example.com',
    key: 'signer-1',
);

$tmpl_key = $bundle->addDocumentTemplate(
    template_id: 'tmpl_abc123',
    assignments: ['signer' => 'signer-1'],          // role => signer key
    initial_field_values: ['agree' => true],        // field key => value
);

// Equivalent, post-hoc:
$bundle->assignRole($tmpl_key, 'signer-1', 'witness');
$bundle->setValue($tmpl_key, 'effective_date', '2026-01-01');

$response = $client->bundles->createFromBundleHelper($bundle);
```

Other Bundle operations:

```php
$client->bundles->retrieve($bundle_id);
$client->bundles->retrieve($bundle_id, related_data: true); // attach events / files / data
$client->bundles->cancel($bundle_id);
$client->bundles->listEvents($bundle_id);
$client->bundles->listFiles($bundle_id);
$client->bundles->listData($bundle_id);
```

### Persons

```php
use Blueink\ClientSDK\PersonHelper;

$helper = new PersonHelper(['name' => 'Jane Doe']);
$helper->addEmail('jane@example.com');
$helper->addPhone('+15551234567');

$created = $client->persons->createFromPersonHelper($helper);
$person_id = $created->data['id'];

$client->persons->retrieve($person_id);
$client->persons->update($person_id, ['metadata' => ['vip' => true]], partial: true);
$client->persons->delete($person_id);
```

> The Blueink API normalizes `name` by splitting on whitespace into first/last
> tokens, so the round-tripped `name` may not be byte-identical to what was sent.

### Packets

```php
$client->packets->update($packet_id, ['email' => 'new@example.com']);
$client->packets->embedURL($packet_id);    // signed embedded-signing URL
$client->packets->retrieveCOE($packet_id); // Certificate of Evidence
$client->packets->remind($packet_id);
```

### Templates

```php
$client->templates->list();
$client->templates->retrieve($template_id);
```

### Envelope Templates

Envelope templates are reusable, end-to-end Bundle workflows configured in
the Blueink dashboard. The subclient is read-only:

```php
$client->envelope_templates->list();
$client->envelope_templates->retrieve($envelope_template_id);

foreach ($client->envelope_templates->pagedList(per_page: 100) as $page) {
    foreach ($page->data as $tpl) {
        echo $tpl['id'] . "\n";
    }
}
```

To create a Bundle from an envelope template, configure a `BundleHelper` with
`setEnvelopeTemplate()` (plus any signers and field overrides) and post it via
`createFromEnvelopeTemplateHelper()`:

```php
use Blueink\ClientSDK\BundleHelper;

$bundle = new BundleHelper([
    'label'   => 'Onboarding paperwork',
    'is_test' => true,
]);
$bundle->addSigner(name: 'Peter Gibbons', email: 'peter@example.com', key: 'signer-1');

$bundle->setEnvelopeTemplate(
    template_id: 'env_tmpl_abc123',
    field_values: ['company_name' => 'ACME Corp'],
);
$bundle->addEnvelopeTemplateFieldValue('start_date', '2026-01-15');

$response = $client->bundles->createFromEnvelopeTemplateHelper($bundle);
```

For pre-built payloads, `createFromEnvelopeTemplate(array $data)` posts the
array directly to `/bundles/create_from_envelope_template/`.

### Webhooks

```php
$created = $client->webhooks->create([
    'name'        => 'My integration',
    'url'         => 'https://example.com/blueink-webhook',
    'event_types' => ['bundle_complete'],
]);

$client->webhooks->update($id, ['url' => '…'], partial: true);
$client->webhooks->delete($id);

// Custom request headers Blueink will send to your endpoint
$client->webhooks->createHeader([
    'webhook' => $id,
    'name'    => 'X-My-Token',
    'value'   => 'shhh',
    'order'   => 0,
]);

// Verification of incoming deliveries
$client->webhooks->retrieveSecret();
$client->webhooks->regenerateSecret();

// Inspection
$client->webhooks->listEvents();
$client->webhooks->listDeliveries();
$client->webhooks->retrieveDelivery($delivery_id);
```

## Pagination

List endpoints accept `page` and `per_page` and return a `Pagination` object on
the response, parsed from the `X-Blueink-Pagination` header:

```php
$response = $client->bundles->list(page: 1, per_page: 25);

$response->pagination->page_number;
$response->pagination->total_pages;
$response->pagination->per_page;
$response->pagination->total_results;
```

For automatic page-walking, every list-capable subclient exposes a `pagedList()`
that returns a `Paginated` iterator. Each iteration yields the next page's
`NormalizedResponse`:

```php
foreach ($client->bundles->pagedList(per_page: 100) as $page) {
    foreach ($page->data as $bundle) {
        // …
    }
}
```

## Error handling

By default 4XX/5XX responses raise the corresponding Guzzle exception
(`GuzzleHttp\Exception\ClientException`, `ServerException`, `ConnectException`,
etc.). The Blueink API returns a structured error body:

```json
{
    "detail": "Invalid input.",
    "code": "invalid",
    "errors": [
        { "field": "channels", "message": "This field is required." }
    ]
}
```

```php
use GuzzleHttp\Exception\BadResponseException;

try {
    $client->persons->create(['name' => 'Jane Doe']);
} catch (BadResponseException $e) {
    $body = json_decode((string) $e->getResponse()->getBody(), true);
    foreach ($body['errors'] ?? [] as $error) {
        printf("%s: %s\n", $error['field'], $error['message']);
    }
}
```

To inspect failures without try/catch, construct the client with
`raise_exceptions: false`. 4XX and 5XX responses then come back as
`NormalizedResponse` objects with `status` and decoded `data`:

```php
$client = new Client(raise_exceptions: false);

$response = $client->persons->create(['name' => 'Jane Doe']);
if ($response->status >= 400) {
    var_dump($response->data);
}
```

## Testing

The SDK ships with two PHPUnit suites:

- **`unit`** (default) — fast, hermetic. Uses Guzzle's `MockHandler` to verify
  request shaping (verb, URL, headers, JSON / multipart body) without touching
  the network.
- **`integration`** — opt-in. Hits a real Blueink environment using the key in
  `BLUEINK_PRIVATE_API_KEY` (and optional `BLUEINK_API_URL`). Tests are skipped
  automatically when the key is absent. Use a sandbox account.

```bash
# Unit suite (runs by default)
./vendor/bin/phpunit

# Integration suite, against your sandbox
BLUEINK_PRIVATE_API_KEY=sk_sandbox_… \
BLUEINK_API_URL=https://sandbox.blueink.com/api/v2 \
    ./vendor/bin/phpunit --testsuite=integration
```

## License

[MIT](LICENSE).

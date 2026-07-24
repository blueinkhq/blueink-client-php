# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2026-07-23

Brings the PHP SDK to APIv2 2.16 feature equivalence, mirroring the .NET
and Python SDK 2.16 work.

### Added

- `VerifySubClient` (`POST /verify/`), exposed as `$client->verify`.
- Bundle draft lifecycle methods: `$client->bundles->update()` (PATCH),
  `$client->bundles->send()` (POST), `$client->bundles->validate()` (PUT),
  plus the corresponding `BundleEndpoints` paths.
- `$client->templates->update()` (PATCH `/templates/{id}/`) for metadata
  updates, plus the `TemplateEndpoints::update()` path.
- `packet_declined` and `bundle_signer_reassigned` event types in
  `EVENT_TYPE`.

## [2.0.0] - 2026-05-07

A major overhaul to bring the PHP SDK to feature parity and UX consistency
with the Blueink Python SDK v1.0.1.

### Breaking changes

- Minimum PHP version raised to **8.1**.
- Namespace changed from `BlueInk\ApiClient` to `Blueink\ClientSDK`. All
  public classes (`Client`, `BundleHelper`, `PersonHelper`,
  `NormalizedResponse`, subclients, models) live under the new namespace.
- 4XX/5XX responses now throw `Blueink\ClientSDK\BlueinkApiError` instead
  of `GuzzleHttp\Exception\ClientException` / `BadResponseException`.
  Network-level errors (`ConnectException`, etc.) continue to bubble up
  unchanged. The original Guzzle exception is preserved on
  `$e->getPrevious()`.
- `getLastResponse()` now returns a `NormalizedResponse` object instead
  of a raw decoded body. Use `$last->data` for the parsed body, plus
  `$last->status`, `$last->headers`, `$last->pagination`,
  `$last->originalResponse`.

### Added

#### `BundleHelper` convenience methods (parity with Python SDK)

- `addSigner(name, email|phone, ...)` — replaces the manual Packet/Person
  scaffolding. Returns the generated packet key for use as an editor.
- `addField(document_key, x, y, w, h, p, kind, editors, label, ...)` —
  one call to add a fixed-coordinate field scoped to specific signers.
- `addAutoPlacement(document_key, kind, search, w, h, offset_x, offset_y,
  editors, ...)` — let the API auto-place a field by searching the
  document text.
- `addDocumentTemplate(template_id, assignments, initial_field_values)` —
  add a Document backed by a template, with role/value bindings. Plus
  post-hoc `assignRole()` and `setValue()`.
- Document inputs: `addDocumentByURL`, `addDocumentByPath`,
  `addDocumentByFile` (multipart), `addDocumentByB64`, `addDocumentByHTML`.

#### Envelope templates (new resource)

- `$client->envelope_templates` (`EnvelopeTemplateSubClient`) with
  `list()`, `retrieve()`, and `pagedList()`.
- `BundleHelper::setEnvelopeTemplate(template_id, field_values)` plus
  `addEnvelopeTemplateFieldValue()`.
- `$client->bundles->createFromEnvelopeTemplateHelper($helper)` and
  `createFromEnvelopeTemplate(array $payload)`.

#### Models

- New: `TemplateRefAssignment`, `AutoPlacement`, `EnvelopeTemplate`,
  `EnvelopeTemplateFieldValue`.
- `Field`: added `v_regex`, `v_regex_msg`, `v_attachment_types`.
- `Document`: added `file_html`, `filename`, `auto_placements`,
  `html_fields_mode`, plus a working `addAutoPlacement()`.
- `TemplateRef`: actually stores `key`; fixed `addAssignment` typo.
- `Bundle`: added `signing_brand`.

#### Tooling

- GitHub Actions CI matrix on PHP 8.1 / 8.2 / 8.3 / 8.4 × Composer
  lowest/highest dependency resolution.
- PHPStan at level 5.
- PHP-CS-Fixer with a PSR-12 base ruleset.
- `.git-blame-ignore-revs` keeps `git blame` clean across the one-time
  PSR-12 reformat.
- 159 unit tests (PHPUnit 10) plus 13 opt-in integration tests against
  sandbox (gated on `BLUEINK_PRIVATE_API_KEY`).

### Changed

- `NormalizedResponse` now mirrors the Python SDK shape: `status`,
  `data`, `headers`, `pagination`, `originalResponse`.
- `Pagination` parses `X-Blueink-Pagination` consistently across all
  list endpoints.
- Every list-capable subclient exposes a `pagedList()` returning a
  `Paginated` iterator for ergonomic page walking.
- The SDK switches to `multipart/form-data` automatically when
  `BundleHelper::addDocumentByFile()` queues a file.
- In `raise_exceptions: false` mode, 4XX/5XX responses are returned as
  `NormalizedResponse` objects with the original status and decoded body
  (matches Python SDK behavior).

### Fixed

- `Authorization` header now uses the correct `Token <key>` format.
- `BundleHelper`: `addAssignment` typo (`assigments` → `assignments`).
- `Helper::removeNullProperties` and a handful of dead `is_null()` /
  `is_object()` checks surfaced by PHPStan.
- `PersonHelper::setEmails` `@param` name corrected.

### Migration

```php
// Before (v1.x)
use BlueInk\ApiClient\Client;
use GuzzleHttp\Exception\ClientException;

$client = new Client('<key>');
try {
    $bundle = $client->bundles->retrieve('abc');
} catch (ClientException $e) {
    $body = json_decode((string) $e->getResponse()->getBody(), true);
}

// After (v2.0)
use Blueink\ClientSDK\Client;
use Blueink\ClientSDK\BlueinkApiError;

$client = new Client('<key>');
try {
    $response = $client->bundles->retrieve('abc');
    $bundle = $response->data;
} catch (BlueinkApiError $e) {
    $e->status_code; $e->detail; $e->api_code; $e->errors;
    // $e->getPrevious() is the original Guzzle exception if needed
}
```

## [1.1.0] - 2021-04-28

- Template subclient and webhook subclient.
- Endpoints update, bundle subclient improvements.
- Naming convention switched from `underscore_case` to `camelCase`.

## [1.0.1] - 2020-02-27

- Maintenance release.

## [1.0.0] - 2020-02-27

- Initial public release.

[Unreleased]: https://github.com/blueinkhq/blueink-client-php/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/blueinkhq/blueink-client-php/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/blueinkhq/blueink-client-php/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/blueinkhq/blueink-client-php/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/blueinkhq/blueink-client-php/releases/tag/v1.0.0

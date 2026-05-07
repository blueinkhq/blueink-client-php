<?php
namespace Blueink\ClientSDK\Tests\Integration;

use Blueink\ClientSDK\Client;
use Blueink\ClientSDK\NormalizedResponse;
use PHPUnit\Framework\TestCase;

/**
 * Base class for tests that hit a real Blueink API.
 *
 * Opt-in:
 *   BLUEINK_PRIVATE_API_KEY  required; tests are skipped without it
 *   BLUEINK_API_URL          optional; point at a sandbox env (recommended)
 *
 * To run only this suite:
 *   BLUEINK_PRIVATE_API_KEY=sk_... \
 *   BLUEINK_API_URL=https://api.sandbox.blueink.com/api/v2 \
 *   ./vendor/bin/phpunit --testsuite=integration --no-coverage
 *
 * @coversNothing
 */
abstract class IntegrationTestCase extends TestCase
{
    protected ?Client $client = null;

    /**
     * IDs of resources created by the test that should be torn down even if
     * the test itself fails. Concrete subclasses populate this and override
     * {@see cleanupResource()} to issue the appropriate delete call.
     *
     * @var array<int, string>
     */
    protected array $cleanup_ids = [];

    protected function setUp(): void
    {
        $key = getenv('BLUEINK_PRIVATE_API_KEY');
        if (!$key) {
            $this->markTestSkipped(
                'Integration tests require BLUEINK_PRIVATE_API_KEY (and optionally BLUEINK_API_URL) to be set.'
            );
        }

        // Client constructor reads BLUEINK_PRIVATE_API_KEY / BLUEINK_API_URL from env.
        $this->client = new Client(raise_exceptions: false);
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanup_ids as $id) {
            try {
                $this->cleanupResource($id);
            } catch (\Throwable) {
                // Best-effort cleanup; swallow so a test failure isn't masked
                // by a cleanup failure.
            }
        }
        $this->cleanup_ids = [];
    }

    /**
     * Subclasses override to remove a resource they created during the test.
     * Default is a no-op so subclasses without cleanup don't need to.
     */
    protected function cleanupResource(string $id): void
    {
    }

    /**
     * Generate a label that makes test artifacts trivially identifiable in
     * the Blueink dashboard, e.g. "[blueink-php-sdk-it] Person 9c4f1e2a".
     */
    protected function uniqueLabel(string $kind): string
    {
        return sprintf('[blueink-php-sdk-it] %s %s', $kind, bin2hex(random_bytes(4)));
    }

    /**
     * Assert that a NormalizedResponse has an acceptable status code, and
     * surface the API's error body in the failure message when it doesn't.
     * Without this, a 400 from the API just shows "expected 201, got 400"
     * which hides the actual reason.
     */
    protected function assertResponseOk(
        NormalizedResponse $response,
        array $accepted = [200, 201],
        string $context = ''
    ): void {
        if (!in_array($response->status, $accepted, true)) {
            $body = is_string($response->data)
                ? $response->data
                : json_encode($response->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $prefix = $context !== '' ? "$context: " : '';
            $this->fail(sprintf(
                "%sexpected status in [%s], got %d. Response body:\n%s",
                $prefix,
                implode(', ', $accepted),
                $response->status,
                $body
            ));
        }
        $this->assertContains($response->status, $accepted);
    }
}

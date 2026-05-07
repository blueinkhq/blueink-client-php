<?php
namespace Blueink\ClientSDK\Tests\Integration;

/**
 * Read-only reachability checks. Verifies the SDK can authenticate, hit each
 * top-level list endpoint, and parse the response. No resources are created.
 *
 * @coversNothing
 */
class SmokeTest extends IntegrationTestCase
{
    public function testBundlesListReachable(): void
    {
        $resp = $this->client->bundles->list(1, 1);

        $this->assertSame(200, $resp->status, "Unexpected status: {$resp->status}");
        $this->assertIsArray($resp->data);
    }

    public function testPersonsListReachable(): void
    {
        $resp = $this->client->persons->list(1, 1);

        $this->assertSame(200, $resp->status);
        $this->assertIsArray($resp->data);
    }

    public function testTemplatesListReachable(): void
    {
        $resp = $this->client->templates->list(1, 1);

        $this->assertSame(200, $resp->status);
        $this->assertIsArray($resp->data);
    }

    public function testWebhooksListReachable(): void
    {
        $resp = $this->client->webhooks->list(1, 1);

        $this->assertSame(200, $resp->status);
        $this->assertIsArray($resp->data);
    }

    public function testPaginationHeaderPopulated(): void
    {
        $resp = $this->client->bundles->list(1, 1);

        $this->assertSame(200, $resp->status);
        // The Blueink API returns X-Blueink-Pagination on list endpoints.
        $this->assertNotNull(
            $resp->pagination,
            'Expected X-Blueink-Pagination header on list response.'
        );
        $this->assertGreaterThanOrEqual(0, $resp->pagination->total_results);
    }
}

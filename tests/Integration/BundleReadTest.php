<?php
namespace Blueink\ClientSDK\Tests\Integration;

/**
 * Read-only Bundle integration tests. Bundle creation requires Documents and
 * Packets and a real signer, so it is intentionally not exercised here; that
 * belongs in a higher-effort fixture-driven test if needed.
 *
 * @coversNothing
 */
class BundleReadTest extends IntegrationTestCase
{
    public function testListReturnsArray(): void
    {
        $resp = $this->client->bundles->list(1, 5);

        $this->assertSame(200, $resp->status);
        $this->assertIsArray($resp->data);
    }

    public function testRetrieveFirstBundleIfAny(): void
    {
        $list = $this->client->bundles->list(1, 1);
        $this->assertSame(200, $list->status);

        if (empty($list->data)) {
            $this->markTestSkipped('Account has no bundles to retrieve.');
        }

        $bundle_id = $list->data[0]['id'] ?? null;
        $this->assertNotEmpty($bundle_id);

        $retrieved = $this->client->bundles->retrieve($bundle_id);
        $this->assertSame(200, $retrieved->status);
        $this->assertSame($bundle_id, $retrieved->data['id']);
    }

    public function testListEventsForFirstBundleIfAny(): void
    {
        $list = $this->client->bundles->list(1, 1);
        $this->assertSame(200, $list->status);

        if (empty($list->data)) {
            $this->markTestSkipped('Account has no bundles.');
        }

        $bundle_id = $list->data[0]['id'];
        $events = $this->client->bundles->listEvents($bundle_id);

        $this->assertSame(200, $events->status);
        $this->assertIsArray($events->data);
    }
}

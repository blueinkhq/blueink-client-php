<?php

namespace Blueink\ClientSDK\Tests\Integration;

use Blueink\ClientSDK\PersonHelper;

/**
 * Full create / read / update / delete cycle against a real Person resource.
 * Created records are tagged with a unique label so they're easy to identify
 * in the dashboard, and torn down in tearDown() even on test failure.
 *
 * @coversNothing
 */
class PersonCrudTest extends IntegrationTestCase
{
    protected function cleanupResource(string $id): void
    {
        $this->client->persons->delete($id);
    }

    public function testCreateRetrieveUpdateDelete(): void
    {
        $name = $this->uniqueLabel('Person');

        $created = $this->client->persons->create([
            'name' => $name,
            'metadata' => ['integration_test' => true],
            'channels' => [
                ['email' => 'integration-test@example.com', 'kind' => 'em'],
            ],
        ]);
        $this->assertResponseOk($created, [200, 201], 'Person create');
        $this->assertNotEmpty($created->data['id'] ?? null);

        $id = $created->data['id'];
        $this->cleanup_ids[] = $id;

        $retrieved = $this->client->persons->retrieve($id);
        $this->assertSame(200, $retrieved->status);
        $this->assertSame($id, $retrieved->data['id']);
        // The Blueink API normalizes `name` by splitting on whitespace and
        // treating the first/last tokens as first/last name, so the round-
        // tripped name may not be byte-equal to what we sent. Assert on the
        // unique suffix instead, which is what actually identifies this record.
        $suffix = substr($name, -8);
        $this->assertStringContainsString($suffix, $retrieved->data['name']);

        $updated = $this->client->persons->update(
            $id,
            ['metadata' => ['integration_test' => true, 'updated' => true]],
            true
        );
        $this->assertSame(200, $updated->status);
        $this->assertTrue($updated->data['metadata']['updated'] ?? false);

        $deleted = $this->client->persons->delete($id);
        $this->assertContains($deleted->status, [200, 202, 204]);

        // Already deleted; don't re-delete in tearDown.
        $this->cleanup_ids = [];
    }

    public function testCreateFromPersonHelper(): void
    {
        $helper = new PersonHelper([
            'name' => $this->uniqueLabel('PersonHelper'),
            'emails' => ['integration-test@example.com'],
            'metadata' => ['integration_test' => true],
        ]);

        $created = $this->client->persons->createFromPersonHelper($helper);
        $this->assertResponseOk($created, [200, 201], 'Person create from helper');

        $id = $created->data['id'] ?? null;
        $this->assertNotEmpty($id);
        $this->cleanup_ids[] = $id;

        // Verify channels round-tripped.
        $channels = $created->data['channels'] ?? [];
        $emails = array_column($channels, 'email');
        $this->assertContains('integration-test@example.com', $emails);
    }
}

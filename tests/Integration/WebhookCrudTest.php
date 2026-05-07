<?php

namespace Blueink\ClientSDK\Tests\Integration;

/**
 * Full webhook + header CRUD against a real account. Webhook target is a
 * placeholder URL that is never exercised because we delete the webhook
 * before any events would fire.
 *
 * @coversNothing
 */
class WebhookCrudTest extends IntegrationTestCase
{
    private array $header_cleanup_ids = [];

    protected function cleanupResource(string $id): void
    {
        $this->client->webhooks->delete($id);
    }

    protected function tearDown(): void
    {
        foreach ($this->header_cleanup_ids as $id) {
            try {
                $this->client->webhooks->deleteHeader($id);
            } catch (\Throwable) {
            }
        }
        $this->header_cleanup_ids = [];

        parent::tearDown();
    }

    public function testWebhookCreateRetrieveUpdateDelete(): void
    {
        $created = $this->client->webhooks->create([
            'name' => $this->uniqueLabel('Webhook'),
            'url' => 'https://example.com/blueink-php-sdk-it/' . bin2hex(random_bytes(4)),
            'event_types' => ['bundle_complete'],
        ]);
        $this->assertResponseOk($created, [200, 201], 'Webhook create');
        $id = $created->data['id'] ?? null;
        $this->assertNotEmpty($id);
        $this->cleanup_ids[] = $id;

        $retrieved = $this->client->webhooks->retrieve($id);
        $this->assertSame(200, $retrieved->status);
        $this->assertSame($id, $retrieved->data['id']);

        $updated = $this->client->webhooks->update(
            $id,
            ['event_types' => ['bundle_complete', 'bundle_cancelled']],
            true
        );
        $this->assertSame(200, $updated->status);

        $deleted = $this->client->webhooks->delete($id);
        $this->assertContains($deleted->status, [200, 202, 204]);
        $this->cleanup_ids = [];
    }

    public function testWebhookHeaderCreateRetrieveUpdateDelete(): void
    {
        // Headers are scoped to a webhook in the Blueink API, so we need a
        // parent webhook to attach them to.
        $webhook = $this->client->webhooks->create([
            'name' => $this->uniqueLabel('Webhook'),
            'url' => 'https://example.com/blueink-php-sdk-it/' . bin2hex(random_bytes(4)),
            'event_types' => ['bundle_complete'],
        ]);
        $this->assertResponseOk($webhook, [200, 201], 'Webhook setup for header CRUD');
        $webhook_id = $webhook->data['id'] ?? null;
        $this->assertNotEmpty($webhook_id);
        $this->cleanup_ids[] = $webhook_id;

        $header = $this->client->webhooks->createHeader([
            'webhook' => $webhook_id,
            'name' => 'X-Integration-Test',
            'value' => bin2hex(random_bytes(4)),
            'order' => 0,
        ]);
        $this->assertResponseOk($header, [200, 201], 'Webhook header create');
        $header_id = $header->data['id'] ?? null;
        $this->assertNotEmpty($header_id);
        $this->header_cleanup_ids[] = $header_id;

        $retrieved = $this->client->webhooks->retrieveHeader($header_id);
        $this->assertSame(200, $retrieved->status);
        $this->assertSame($header_id, $retrieved->data['id']);

        $updated = $this->client->webhooks->updateHeader(
            $header_id,
            ['value' => 'updated-' . bin2hex(random_bytes(2))],
            true
        );
        $this->assertSame(200, $updated->status);

        $deleted = $this->client->webhooks->deleteHeader($header_id);
        $this->assertContains($deleted->status, [200, 202, 204]);
        $this->header_cleanup_ids = [];
    }

    public function testRetrieveSecret(): void
    {
        $resp = $this->client->webhooks->retrieveSecret();

        $this->assertSame(200, $resp->status);
        $this->assertNotEmpty($resp->data['secret'] ?? null);
    }
}

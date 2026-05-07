<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\BundleHelper;
use Blueink\ClientSDK\BundleSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\BundleSubClient
 * @covers \Blueink\ClientSDK\SubClient
 * @covers \Blueink\ClientSDK\BundleEndpoints
 */
class BundleSubClientHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    private function client(array $responses): array
    {
        $built = MockHttpFactory::buildRequestHelper($responses);
        $sub = new BundleSubClient(self::BASE, $built['request']);

        return ['sub' => $sub, 'history' => &$built['history']];
    }

    public function testCreateSendsJsonPostWhenNoFiles(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"bun_1"}')]);
        $payload = ['label' => 'Hi', 'documents' => [], 'packets' => []];

        $resp = $built['sub']->create($payload);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/', (string) $req->getUri());
        $this->assertSame($payload, json_decode((string) $req->getBody(), true));
        $this->assertSame('bun_1', $resp->data['id']);
    }

    public function testCreateSendsMultipartWhenFilesPresent(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"bun_2"}')]);
        $tmp = tempnam(sys_get_temp_dir(), 'blueink_test_');
        file_put_contents($tmp, '%PDF-1.4 test');

        try {
            $built['sub']->create([
                'label'      => 'Hi',
                'documents'  => [['key' => 'doc1', 'file_index' => 0]],
                'packets'    => [],
                'file_names' => ['contract.pdf'],
                'file_types' => ['application/pdf'],
                'files'      => [$tmp],
            ]);
        } finally {
            @unlink($tmp);
        }

        $req = $built['history'][0]['request'];
        $this->assertStringStartsWith('multipart/form-data', $req->getHeaderLine('Content-Type'));
        $body = (string) $req->getBody();
        $this->assertStringContainsString('name="bundle_request"', $body);
        $this->assertStringContainsString('name="files[0]"', $body);
        $this->assertStringContainsString('filename="contract.pdf"', $body);
    }

    public function testCreateThrowsOnNullData(): void
    {
        $built = $this->client([]);

        $this->expectException(\InvalidArgumentException::class);
        $built['sub']->create(null);
    }

    public function testCreateFromBundleHelperUsesAsData(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"bun_3"}')]);
        $helper = new BundleHelper(['label' => 'From Helper', 'is_test' => true]);

        $built['sub']->createFromBundleHelper($helper);

        $sent = json_decode((string) $built['history'][0]['request']->getBody(), true);
        $this->assertSame('From Helper', $sent['label']);
        $this->assertTrue($sent['is_test']);
    }

    public function testListPassesPaginationAndFiltersAsQuery(): void
    {
        $built = $this->client([new Response(200, [], '[]')]);

        $built['sub']->list(2, 25, false, ['status' => 'co']);

        $uri = $built['history'][0]['request']->getUri();
        parse_str($uri->getQuery(), $q);
        $this->assertSame('2', $q['page']);
        $this->assertSame('25', $q['per_page']);
        $this->assertSame('co', $q['status']);
        $this->assertSame(self::BASE . '/bundles/', $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath());
    }

    public function testRetrieveHitsBundlePath(): void
    {
        $built = $this->client([new Response(200, [], '{"id":"bun_X"}')]);

        $built['sub']->retrieve('bun_X');

        $req = $built['history'][0]['request'];
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/bun_X/', (string) $req->getUri());
    }

    public function testRetrieveWithRelatedDataAttachesEvents(): void
    {
        $built = $this->client([
            new Response(200, [], '{"id":"bun_X","status":"se"}'),
            new Response(200, [], '[{"type":"sent"}]'),
        ]);

        $resp = $built['sub']->retrieve('bun_X', true);

        $this->assertCount(2, $built['history']);
        $this->assertSame(self::BASE . '/bundles/bun_X/events/', (string) $built['history'][1]['request']->getUri());
        $this->assertSame([['type' => 'sent']], $resp->data['events']);
    }

    public function testRetrieveWithRelatedDataAttachesFilesAndDataWhenComplete(): void
    {
        $built = $this->client([
            new Response(200, [], '{"id":"bun_X","status":"co"}'),
            new Response(200, [], '[{"type":"complete"}]'),
            new Response(200, [], '[{"name":"f.pdf"}]'),
            new Response(200, [], '[{"key":"k","value":"v"}]'),
        ]);

        $resp = $built['sub']->retrieve('bun_X', true);

        $this->assertCount(4, $built['history']);
        $this->assertSame(self::BASE . '/bundles/bun_X/files/', (string) $built['history'][2]['request']->getUri());
        $this->assertSame(self::BASE . '/bundles/bun_X/data/', (string) $built['history'][3]['request']->getUri());
        $this->assertSame([['key' => 'k', 'value' => 'v']], $resp->data['data']);
    }

    public function testCancelIssuesPut(): void
    {
        $built = $this->client([new Response(200, [], '{}')]);

        $built['sub']->cancel('bun_X');

        $req = $built['history'][0]['request'];
        $this->assertSame('PUT', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/bun_X/cancel/', (string) $req->getUri());
    }

    public function testListEventsFilesData(): void
    {
        $built = $this->client([
            new Response(200, [], '[]'),
            new Response(200, [], '[]'),
            new Response(200, [], '[]'),
        ]);

        $built['sub']->listEvents('b1');
        $built['sub']->listFiles('b1');
        $built['sub']->listData('b1');

        $this->assertSame(self::BASE . '/bundles/b1/events/', (string) $built['history'][0]['request']->getUri());
        $this->assertSame(self::BASE . '/bundles/b1/files/', (string) $built['history'][1]['request']->getUri());
        $this->assertSame(self::BASE . '/bundles/b1/data/', (string) $built['history'][2]['request']->getUri());
    }

    public function testCreateFromEnvelopeTemplatePostsJsonToEnvelopeEndpoint(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"bun_E"}')]);
        $payload = [
            'packets'           => [['key' => 'sgn-1', 'name' => 'A', 'email' => 'a@x.com']],
            'envelope_template' => ['template_id' => 'T-abc'],
            'is_test'           => true,
        ];

        $resp = $built['sub']->createFromEnvelopeTemplate($payload);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/create_from_envelope_template/', (string) $req->getUri());
        $this->assertSame($payload, json_decode((string) $req->getBody(), true));
        $this->assertSame('bun_E', $resp->data['id']);
    }

    public function testCreateFromEnvelopeTemplateHelperUsesAsDataForEnvelopeTemplate(): void
    {
        $built = $this->client([new Response(201, [], '{"id":"bun_F"}')]);

        $bh = new BundleHelper(['label' => 'Env Bundle', 'is_test' => true]);
        $bh->addSigner(name: 'Jane', email: 'jane@example.com', key: 'sgn-1');
        $bh->setEnvelopeTemplate('T-xyz', ['company' => 'ACME']);

        $built['sub']->createFromEnvelopeTemplateHelper($bh);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/create_from_envelope_template/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('T-xyz', $body['envelope_template']['template_id']);
        $this->assertSame('company', $body['envelope_template']['field_values'][0]['key']);
        $this->assertSame('ACME', $body['envelope_template']['field_values'][0]['initial_value']);
        $this->assertSame('jane@example.com', $body['packets'][0]['email']);
        $this->assertSame('Env Bundle', $body['label']);
        $this->assertTrue($body['is_test']);
    }
}

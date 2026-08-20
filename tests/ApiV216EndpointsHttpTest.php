<?php

namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\BundleSubClient;
use Blueink\ClientSDK\TemplateSubClient;
use Blueink\ClientSDK\Tests\Support\MockHttpFactory;
use Blueink\ClientSDK\VerifySubClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * CI-safe (mocked) coverage for the APIv2 2.16 endpoints.
 *
 * @covers \Blueink\ClientSDK\BundleSubClient
 * @covers \Blueink\ClientSDK\TemplateSubClient
 * @covers \Blueink\ClientSDK\VerifySubClient
 * @covers \Blueink\ClientSDK\BundleEndpoints
 * @covers \Blueink\ClientSDK\TemplateEndpoints
 * @covers \Blueink\ClientSDK\VerifyEndpoints
 */
class ApiV216EndpointsHttpTest extends TestCase
{
    private const BASE = 'https://api.example.com/api/v2';

    public function testBundleUpdatePatchesBundlePath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{"id":"B-1"}')]);
        $sub = new BundleSubClient(self::BASE, $built['request']);

        $sub->update('B-1', ['signing_brand' => 'SB-1']);

        $req = $built['history'][0]['request'];
        $this->assertSame('PATCH', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/B-1/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('SB-1', $body['signing_brand']);
    }

    public function testBundleSendPostsSendPath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{}')]);
        $sub = new BundleSubClient(self::BASE, $built['request']);

        $sub->send('B-1');

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/B-1/send/', (string) $req->getUri());
    }

    public function testBundleValidatePutsValidatePath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{"can_send":true}')]);
        $sub = new BundleSubClient(self::BASE, $built['request']);

        $sub->validate('B-1');

        $req = $built['history'][0]['request'];
        $this->assertSame('PUT', $req->getMethod());
        $this->assertSame(self::BASE . '/bundles/B-1/validate/', (string) $req->getUri());
    }

    public function testTemplateUpdatePatchesTemplatePath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{"id":"tpl_1"}')]);
        $sub = new TemplateSubClient(self::BASE, $built['request']);

        $sub->update('tpl_1', ['metadata' => ['a' => 1]]);

        $req = $built['history'][0]['request'];
        $this->assertSame('PATCH', $req->getMethod());
        $this->assertSame(self::BASE . '/templates/tpl_1/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame(1, $body['metadata']['a']);
    }

    public function testVerifyCreatePostsVerifyPath(): void
    {
        $built = MockHttpFactory::buildRequestHelper([new Response(200, [], '{"verified":true}')]);
        $sub = new VerifySubClient(self::BASE, $built['request']);

        $sub->create(['hash' => 'deadbeef']);

        $req = $built['history'][0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame(self::BASE . '/verify/', (string) $req->getUri());
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('deadbeef', $body['hash']);
    }

    public function testEventTypeConstantsInclude216Values(): void
    {
        $values = array_values(\Blueink\ClientSDK\EVENT_TYPE);
        $this->assertContains('packet_declined', $values);
        $this->assertContains('bundle_signer_reassigned', $values);
    }

    public function testApiV218ConstantsAndExpires(): void
    {
        $this->assertSame('stp', \Blueink\ClientSDK\FIELD_KIND['STAMP']);
        $this->assertSame('ra', \Blueink\ClientSDK\PACKET_STATUS['REASSIGNED']);

        $helper = new \Blueink\ClientSDK\BundleHelper([
            'label' => 'expires-test',
            'expires' => '2026-12-31T23:59:59Z',
            'signing_brand' => 'brand-1',
        ]);
        $helper->addDocumentByURL('https://example.com/doc.pdf');
        $helper->addSigner(name: 'Alice', email: 'alice@example.com');
        $data = $helper->asData();
        $this->assertSame('2026-12-31T23:59:59Z', $data['expires']);
        $this->assertSame('brand-1', $data['signing_brand']);
    }
}

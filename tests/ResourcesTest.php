<?php
/**
 */

use BlueInk\ApiClient\BundleResource;
use BlueInk\ApiClient\Client;
use Snorlax\RestClient;
use PHPUnit\Framework\TestCase;

class ResourcesTest extends TestCase
{
    public function testBaseUriEnv()
    {
        putenv(BundleResource::ENV_BLUEINK_API_URI . '=http://blueink.example.com/api/1.5');
        $client = new RestClient([]);
        $resource = new BundleResource($client);
        $this->assertEquals('http://blueink.example.com/api/1.5/bundles', $resource->getBaseUri());

        // unset to not interfere with future tests
        putenv(BundleResource::ENV_BLUEINK_API_URI);
    }

    public function testBaseUriDefault()
    {
        $resource = new BundleResource(new RestClient([]));
        $this->assertEquals($resource::DEFAULT_BASE_URI . '/bundles', $resource->getBaseUri());
    }

    public function testBaseUriClient()
    {
        $client = new Client('FAKE_AUTH_TOKEN', ['baseUri' => 'http://foobar.example.com']);
        $resource = new BundleResource($client);

        $this->assertEquals('http://foobar.example.com/bundles', $resource->getBaseUri());
    }

}

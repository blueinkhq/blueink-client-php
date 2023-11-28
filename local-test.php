<?php
# NOTE
## This file using for testing method in developing process
## Should copy .env.example to .env and change the value of variable
## Need to remove this file when release

require_once "src/blueink/Client.php";
require_once "src/blueink/models/Bundles.php";
use GuzzleHttp\Exception as GuzzleException;
use Blueink\ClientSDK as Blueink;

# NOTE
## This block is using for get ENV VARIABLE to test rather than install phpdotenv
## This block should be remove when release, the example will be on other repository 
### --- start block --- ###
$env = file_get_contents(__DIR__ . "/.env");
$lines = explode("\n", $env);
foreach ($lines as $line) {
    preg_match("/([^#]+)\=(.*)/", $line, $matches);
    if (isset($matches[2])) {
        putenv(trim($line));
    }
}
### --- end block --- ###
function test_bundles()
{
    $_name = 'SDK NAME TEST';
    $_email = 'example_email@email.com';
    $_file_url = 'https://www.irs.gov/pub/irs-pdf/fw9.pdf';
    $client = new Blueink\Client(getenv("BLUEINK_PRIVATE_API_KEY"));
    $packet = Blueink\Packet::create(null, $_name, ['email' => $_email]);

    $document = Blueink\Document::create(null, ['file_url' => $_file_url]);
    $bundle = new Blueink\Bundle(['packets' => [$packet], 'documents' => [$document]]);

    try {
        $new_bundle = $client->bundles->create(['body' => $bundle]);
        $client->bundles->list('2', '1');
        $client->bundles->cancel($new_bundle['id']);
        $client->bundles->retrieve($new_bundle['id']);
    } catch (GuzzleException\RequestException $e) {
        # handle the exception
        echo 'Got an exception';
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Reason: " . $response->getReasonPhrase() . "\n";
    }
}

function test_persons()
{
    $json = '{
        "name": "Tom Jones",
        "metadata": {
            "occupation": "legend"
        },
        "channels": [
            {
                "email": "tom.jones@example.com",
                "kind": "em"
            },
            {
                "phone": "505 555 1234",
                "kind": "mp"
            }
        ]
    }';
    $json2 = '{
        "name": "=== Tom Jones",
        "metadata": {
            "occupation": "legend"
        },
        "channels": [
            {
                "email": "tom.jones@example.com",
                "kind": "em"
            },
            {
                "phone": "505 555 1234",
                "kind": "mp"
            }
        ]
    }';
    $json3 = [
        'name' => 'ccnebao',
        'metadata' => ['occupation' => 'legend'],
        'channels' => [
            ['email' => 'ccnebao@gmail.com', 'kind' => 'em'],
            ['phone' => '505 555 1234', 'kind' => 'mp']
        ]
    ];
    $client = new Blueink\Client(getenv('BLUEINK_PRIVATE_API_KEY'));
    try {
        $person = $client->persons->create(['body' => $json3]);
        $client->persons->list('2', '1');
        $client->persons->update($person['id'], ['json' => $json]);
        $client->persons->retrieve($person['id']);
        $client->persons->delete($person['id']);
    } catch (GuzzleException\RequestException $e) {
        # handle the exception
        echo 'Got an exception';
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Reason: " . $response->getReasonPhrase() . "\n";
    }
}

function test_packet()
{
    $client = new Blueink\Client(getenv('BLUEINK_PRIVATE_API_KEY'));

    try {
        $client->packets->retrieve_coe('cc');
    } catch (GuzzleException\RequestException $e) {
        # handle the exception
        echo 'Got an exception';
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Reason: " . $response->getReasonPhrase() . "\n";
    }
}

function test_template()
{
    $client = new Blueink\Client(getenv('BLUEINK_PRIVATE_API_KEY'));

    try {
        $client->templates->list();
        $client->templates->retrieve('cc');
    } catch (GuzzleException\RequestException $e) {
        # handle the exception
        echo 'Got an exception';
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Reason: " . $response->getReasonPhrase() . "\n";
    }
}

function test_webhook()
{
    $client = new Blueink\Client(getenv('BLUEINK_PRIVATE_API_KEY'));
    $web_data = '{
        "url": "http://example.test",
        "event_types": ["bundle_sent", "bundle_complete"],
        "enabled": true,
        "json": true
    }';
    $header_data = '{
        "id": "4d4aa39c-028a-8409-7c53-4a91fd9a7eb2",
        "webhook": "58a68c1b-e56e-54a2-7677-c7dc928ce8c6",
        "name": "deserunt",
        "value": "minim deserunt cillum sint",
        "order": 1
    }';
    try {
        $webhook = $client->webhooks->create(['json' => $web_data]);
        $client->webhooks->list();
        $client->webhooks->retrieve($webhook['id']);
        $client->webhooks->delete($webhook['id']);
        $header = $client->webhooks->create_header(['json' => $header_data]);
        $client->webhooks->list_headers();
        $client->webhooks->retrieve_header($header['id']);
        $client->webhooks->delete_header($header['id']);
    } catch (GuzzleException\RequestException $e) {
        # handle the exception
        echo 'Got an exception';
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Reason: " . $response->getReasonPhrase() . "\n";
    }
}
// test_bundles();
// test_persons();
// test_packet();
// test_template();
test_webhook();

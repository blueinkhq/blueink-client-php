<?php
# NOTE
## This file using for testing method in developing process
## Should copy .env.example to .env and change the value of variable
## Need to remove this file when release

require_once "src/blueink/Client.php";
require_once "src/blueink/models/Bundles.php";
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
$_name = 'SDK NAME TEST';
$_email = 'example_email@email.com';
$_file_url = 'https://www.irs.gov/pub/irs-pdf/fw9.pdf';
$client = new Blueink\Client(getenv("BLUEINK_PRIVATE_API_KEY"));
$packet = Blueink\Packet::create(null, $_name, ['email' => $_email]);

$document = Blueink\Document::create(null, ['file_url' => $_file_url]);
$bundle = new Blueink\Bundle(['packets' => [$packet], 'documents' => [$document]]);

// $new_bundle = $client->bundles->create($bundle);
// $client->bundles->list('2', '2');
$client->bundles->retrieve('xxx');
// $client->bundles->cancel($new_bundle['id']);

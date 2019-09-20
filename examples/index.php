<?php
/**
 * Created by PhpStorm. Examples of using the BlueInk PHP API.
 *
 * Most of these functions require a Client instance as an parameter.
 * You can create a Client like so:
 *
 *     $client = new Client('YOUR-API-KEY-GOES-HERE', [
 *         'baseUri' => 'https://sandbox.blueink.com/api/v1'
 *     ]);
 *
 * Add your API Key as the first parameter, and set the baseUri appropriately. This is
 * likely the URL to the BlueInk sandbox (https://sandbox.blueink.com/api/v1), or your
 * account specific API URL (e.g. https://example-company.blueink.com/api/v1)
 *
 * Date: 11/29/17
 * Time: 10:22 AM
 */

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use BlueInk\ApiClient\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;


$EXAMPLE_HTML_01 = <<<EOT
    <!DOCTYPE html>
    <html lang="en">
        <head>
        </head>
        <body>
            <h1>Please Sign Below</h1>
            <p>@@signature@@</p>
            <p>@@signature_date@@</p>
        </body>
    </html>
EOT;

/**
 * Create a bundle from HTML, with a single signer
 *
 * @param object $client A BlueInk\APIClient\Client instance
 * @param string $html The HTML to use to generate a new Bundle
 * @param string $bundle_template_slug A slug identifying an existing Bundle Template in your account
 * @param string $signer_name Optional signer name. Defaults to John Doe.
 * @param string $signer_email Optional signer email. Defaults to john.doe@example.com.
 * @param string $signer_phone Optional signer phone. Defaults to 505-555-1212.
 * @param boolean $send - true to send bundle; false to create the bundle and not send it.
 * @return object The newly created Bundle
 */
function create_bundle_from_html(
    $client,
    $html,
    $bundle_template_slug,
    $signer_name = null,
    $signer_email = null,
    $signer_phone = null,
    $send = true,
    $alt = false,
    $custom_key = null,
    $custom_text = ''
) {
    $signer_name = is_null($signer_name) ? 'John Doe' : $signer_name;
    $signer_email = is_null($signer_email) ? 'john.doe@example.com' : $signer_email;
    $signer_phone = is_null($signer_phone) ? '505-555-1212' : $signer_phone;

    $request_data = [
        'json' => [
            'html' => $html,
            'template_slug' => $bundle_template_slug,
            'signers' => [
                'signer' => [
                    'name' => $signer_name,
                    'email' => $signer_email,
                    'phone' => $signer_phone,
                ],
            ],
            'send' => $send,
            'custom_key' => $custom_key,
            'custom_text' => $custom_text,
        ]
    ];

    if ($alt) {
        $new_bundle = $client->bundles->createFromHTMLAlt($request_data);
    } else {
        $new_bundle = $client->bundles->createFromHTML($request_data);
    }

    return $new_bundle;
}

/**
 * Example of creating a Bundle.
 *
 * @param $client
 * @param array $data associative array containing fields used to create Bundle. See API Docs for possible fields
 * @return mixed
 */
function create_bundle($client, $data) {

	if (empty($data)){
		throw new InvalidArgumentException("Data cannot be empty.");
	}

	$bundle = $client->bundles->create([
		'json' => $data
	]);

	// print bundle update
	echo "Bundle: " . $bundle->slug . " created.\n\n\n";

	return $bundle;
}

/**
 * Example of sending a bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return mixed
 */
function send_bundle($client, $bundle_slug) {
	$bundle = $client->bundles->send($bundle_slug);

	// print bundle that was sent
	echo "Bundle  " . $bundle_slug . " has been sent\n";
	echo "\n";
}

/**
 * Example of getting a list of all packets for a particular bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return array
 */
function get_packet_list($client, $bundle_slug) {
	$bundle = $client->bundles->packets($bundle_slug);

	// print list of packets
	echo "Packets for bundle " . $bundle_slug . ":\n";
	echo "\n";
}

/**
 * Example of creating a packet
 *
 * Note: Data is the array containing the request body. Please refer docs to see available parameters.
 *
 * @param $client
 * @param $bundle_slug
 * @param $data
 * @return mixed
 */
function create_packet(
	$client,
	$bundle_slug,
	$data
) {

	if (empty($data)){
		throw new InvalidArgumentException("Data cannot be empty.");
	}

	$packet = $client->bundles->createPacket($bundle_slug, [
		'json' => $data
	]);

	// print bundle update
	echo "Packet: " . $packet->slug . " created.\n\n\n";

	return $packet;
}

/**
 * Example of getting all bundles
 *
 * @param $client
 * @return mixed
 */
function get_all_bundles($client) {
    // get all bundles
    $data = $client->bundles->all();
    $bundles = $data->results;

    // print out all bundles
    echo "All Bundles: \n";

    for ($i = 0; $i < count($bundles); $i++) {
        var_dump($bundles[$i]);
    }

    echo "\n\n\n";

    return $bundles;
}

/**
 * Example of getting a single bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return mixed
 */
function get_single_bundle($client, $bundle_slug) {
    $bundle = $client->bundles->get($bundle_slug);

    // print fetched bundle
    echo "Bundle Fetched - " . $bundle_slug . ":\n";
    var_dump($bundle);
    echo "\n";

    return $bundle;
}

/**
 * Example for deleting a bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return mixed

 */
function delete_bundle($client, $bundle_slug) {
	$bundle = $client->bundles->delete($bundle_slug);

	echo "Bundle " . $bundle_slug . " deleted \n\n";
}

/**
 * Example for canceling a bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return mixed
 */
function cancel_bundle($client, $bundle_slug) {
	$bundle = $client->bundles->cancel($bundle_slug);

	echo "Bundle " . $bundle_slug . " canceled \n\n";
}

/**
 * Example of updating a bundle
 *
 * Note: Data is the array containing the request body (i.e., in_order, is_template, template).
 *
 * @param $client
 * @param $bundle_slug
 * @param $data
 * @return mixed
 */
function update_bundle(
	$client,
	$bundle_slug,
	$data
) {

	if (empty($data)){
		throw new InvalidArgumentException("Data cannot be empty.");
	}

	$updated_bundle = $client->bundles->update($bundle_slug, [
		'json' => $data
	]);

	// print bundle update
	echo "Bundle Updated: " . $updated_bundle->slug . "\n\n\n";

	return $updated_bundle;
}
/**
 * Example of partially updating a bundle
 *
 * Note: Data is the array containing the request body (i.e., in_order, is_template, template).
 *
 * @param $client
 * @param $bundle_slug
 * @param $data
 * @return mixed
 */
function partial_update_bundle(
	$client,
	$bundle_slug,
	$data
) {

	if (empty($data)){
		throw new InvalidArgumentException("Data cannot be empty.");
	}

	$partial_updated_bundle = $client->bundles->update($bundle_slug, [
		'json' => $data
	]);

	// print bundle update
	echo "Bundle partially updated: " . $partial_updated_bundle->slug . "\n\n\n";

	return $partial_updated_bundle;
}

/**
 * Example of error handling for
 *
 * @param $client
 * @param $html
 * @param $bundle_template_slug
 * @param null $signer_name
 * @param null $signer_email
 * @param null $signer_phone
 * @param null $send
 * @return mixed
 */
function handle_errors(
    $client,
    $html,
    $bundle_template_slug,
    $signer_name = null,
    $signer_email = null,
    $signer_phone = null,
    $send = null
) {
    try {
        $new_bundle = create_bundle_from_html($client, $html, $bundle_template_slug, $signer_name, $signer_email, $signer_phone, $send);
    }
    catch (ClientException $e) {
        echo "Got 4XX error:\n" . Psr7\str($e->getResponse()) . "\n\n\n";

        return null;
    }
    catch (ServerException $e) {
        echo "Got 5XX error:\n" . Psr7\str($e->getResponse()) . "\n\n\n";

        return null;
    }

    return $new_bundle;
}

/**
 * Example of getting all events for a particular bundle
 *
 * @param $client
 * @param $bundle_slug
 * @return mixed
 */
function get_bundle_events($client, $bundle_slug) {
    $events = $client->bundles->events($bundle_slug);

    // print out all bundles
    echo "All Events from Bundle " . $bundle_slug .  ": \n";

    for ($i = 0; $i < count($events); $i++) {
        var_dump($events[$i]);
    }

    echo "\n\n\n";

    return $events;
}

/**
 * Example of getting all events for all bundles
 *
 * @param $client
 * @return mixed
 */
function get_events($client) {
    $events = $client->events->all();

    // print out all bundles
    echo "All Events: \n";

    for ($i = 0; $i < count($events); $i++) {
        var_dump($events[$i]);
    }

    echo "\n\n\n";

    return $events;
}

/**
 * Example of getting an event
 *
 * @param $client
 * @param $eventID
 * @return mixed
 */
function get_event($client, $eventID) {
    $event = $client->events->get($eventID);

    echo "Event " . $eventID . ": \n";

    var_dump($event);

    echo "\n\n\n";

    return $event;
}
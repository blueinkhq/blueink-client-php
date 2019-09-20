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

// coming soon
<?php
/**
 */

namespace BlueInk\ApiClient;

use InvalidArgumentException;
use Snorlax\RestClient;

class Client extends RestClient
{
    const ENV_BLUEINK_API_TOKEN = 'BLUEINK_API_TOKEN';
    protected $baseUri = '';

    /**
     * Client constructor. Like the RestClient constructor, but requires an $auth_token and
     * allows setting a base URI via $config which can be used by RestResources (at least those that know
     * about it - see BaseResource).
     * @param array $auth_token
     * @param array $config. Like $config passed to RestClient, but can contain an optional baseUri key, which
     *  will be accessible on any RestResource instance derived from BaseResource via the getClientBaseUri() method.
     *  If baseUri is not passed in the the $config, then the value of environment variable BLUEINK_API_URL is used,
     *  or the hardcoded default (see BaseResource).
     */
    public function __construct($auth_token = null, array $config = [])
    {
        if (!$auth_token) {
            $auth_token = getenv(self::ENV_BLUEINK_API_TOKEN);
        }

        if (!$auth_token) {
            throw new InvalidArgumentException('$auth_token not provided, and not found in environment.');
        }

        if (array_key_exists('baseUri', $config)) {
            $this->baseUri = $config['baseUri'];
            unset($config['baseUri']);
        }

        $merge_config = [
            'resources' => [
                'bundles' => BundleResource::class,
                'packets' => PacketResource::class,
                'persons' => PersonResource::class,
                'templates' => TemplateResource::class,
                'apps' => AppResource::class,
                'account' => AccountResource::class,
            ]
        ];

        $config = array_merge_recursive($config, $merge_config);
        parent::__construct($config);

        $this->setAuthorization(new TokenAuth($auth_token));
    }

    /**
     * Get the baseUri set on this Client instance, if any.
     *
     * This is named getClientBaseUri to avoid confusion with RestResource->getBaseUri().
     *
     * @return string the baseUri (possibly an empty string)
     */
    public function getClientBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Extract BlueInks custom pagination header (X-BlueInk-Pagination), if present,
     * and return the parsed results.
     * @param $response a Response object, as returned by $client->someResource->getLastResponse()
     * @return Null if the pagination header was not found. Otherwise, an array with a shape like the following.
     *      [
     *          'currentPage' => 1,
     *          'totalPages' => 22,
     *          'perPage' => 50,
     *          'totalResults' => 1071,
     *      ]
     */
    public static function getPagination($response) {
        $pagination_header = $response->getHeader('X-BlueInk-Pagination');
        if ($pagination_header) {
            return self::parsePaginationHeader($pagination_header);
        }

        return null;
    }

    /**
     * Parse a BlueInk pagination header value
     * @param $pagination_header the value of X-BlueInk-Pagination
     * @return An array with a shape like the following.
     *      [
     *          'currentPage' => 1,
     *          'totalPages' => 22,
     *          'perPage' => 50,
     *          'totalResults' => 1071,
     *      ]
     */
    public static function parsePaginationHeader($pagination_header) {
        list($current, $total, $per, $results) = explode(',', $pagination_header);
        return [
            'currentPage' => intval($current),
            'totalPages' => intval($total),
            'perPage' => intval($per),
            'totalResults' => intval($results),
        ];
    }
}
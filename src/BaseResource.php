<?php
/**
 */

namespace BlueInk\ApiClient;

use Snorlax\RestResource;

abstract class BaseResource extends RestResource
{
    /**
     * Name of the environment variable that can be set to configure the base BlueInk API URI.
     */
    const ENV_BLUEINK_API_URI = 'BLUEINK_API_URI';
    const DEFAULT_BASE_URI = 'https://api.blueink.com/api/v2';

    /**
     * @var string Default base URI for BlueInk API. Used if a base URI is not specified
     * via client or env
     */
    protected $defaultBaseUri = self::DEFAULT_BASE_URI;

    /**
     * Get the base URI for API calls.
     *
     * The base URI will first, be read from the associated
     * RestClient instance ($this->client), second from the environment (set as BLUEINK_API_URL) and
     * third from the default specified as a class attribute. The first base URI found is used.
     *
     * @return string the base URI
     */
    public function getBaseUri()
    {
        $base_uri = '';

        if (method_exists($this->client, 'getClientBaseUri')) {
            $base_uri = $this->client->getClientBaseUri();
        }

        if (!$base_uri) {
            $base_uri = getenv(self::ENV_BLUEINK_API_URI);
        }

        if (!$base_uri) {
            $base_uri = $this->defaultBaseUri;
        }

        return rtrim($base_uri, '/');
    }
}
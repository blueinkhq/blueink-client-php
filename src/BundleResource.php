<?php
/** Client resource for accessing all bundle-related endpoints
 */

namespace BlueInk\ApiClient;

class BundleResource extends BaseResource
{
    public function getBaseUri()
    {
        return parent::getBaseUri() . '/bundles';
    }

    public function getActions()
    {
        return [
            'list' => [
                'method' => 'GET',
                'path' => '/',
            ],
            'retrieve' => [
                'method' => 'GET',
                'path' => '/{0}/',
            ],
            'create' => [
                'method' => 'POST',
                'path' => '/',
            ],
            'cancel' => [
                 'method' => 'PUT',
                 'path' => '/{0}/cancel/',
            ],
            'delete' => [
                 'method' => 'DELETE',
                 'path' => '/{0}/',
            ],
            'events' => [
                'method' => 'GET',
                'path' => '/{0}/events/',
            ],
            'files' => [
                'method' => 'GET',
                'path' => '/{0}/files/',
            ],
			'data' => [
				'method' => 'GET',
				'path' => '/{0}/data/',
			],
        ];
    }
}
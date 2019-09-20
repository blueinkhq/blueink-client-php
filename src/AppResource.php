<?php
/** Client resource for accessing event-related endpoints
 */

namespace BlueInk\ApiClient;


class AppResource extends BaseResource
{
    public function getBaseUri()
    {
        return parent::getBaseUri() . '/apps';
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
        ];
    }
}
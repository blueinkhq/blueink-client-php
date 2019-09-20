<?php
/** Client resource for accessing event-related endpoints
 */

namespace BlueInk\ApiClient;


class PersonResource extends BaseResource
{
    public function getBaseUri()
    {
        return parent::getBaseUri() . '/persons';
    }

    public function getActions()
    {
        return [
            'list' => [
                'method' => 'GET',
                'path' => '/',
            ],
            'create' => [
                'method' => 'POST',
                'path' => '/',
            ],
            'retrieve' => [
                'method' => 'GET',
                'path' => '/{0}/',
            ],
            'update' => [
                'method' => 'PUT',
                'path' => '/{0}/',
            ],
            'partialUpdate' => [
                'method' => 'PATCH',
                'path' => '/{0}/',
            ],
            'delete' => [
                'method' => 'DELETE',
                'path' => '/{0}/',
            ],
        ];
    }
}
<?php
/** Client resource for accessing event-related endpoints
 */

namespace BlueInk\ApiClient;


class AccountResource extends BaseResource
{
    public function getBaseUri()
    {
        return parent::getBaseUri() . '/account';
    }

    public function getActions()
    {
        return [
            'retrieve' => [
                'method' => 'GET',
                'path' => '/',
            ],
        ];
    }
}
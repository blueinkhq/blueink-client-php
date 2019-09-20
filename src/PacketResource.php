<?php
/**
 * Created by PhpStorm.
 * User: zlovelady
 * Date: 11/22/17
 * Time: 1:34 PM
 *
 * Client resource for accessing all bundle-related endpoints
 */

namespace BlueInk\ApiClient;


class PacketResource extends BaseResource
{
    public function getBaseUri()
    {
        return parent::getBaseUri() . '/packets';
    }

    public function getActions()
    {
        return [
            'retrieve' => [
                'method' => 'GET',
                'path' => '/{0}/',
            ],
            'update' => [
                'method' => 'PATCH',
                'path' => '/{0}/',
            ],
            'remind' => [
                'method' => 'PUT',
                'path' => '/{0}/remind/',
            ],
            'coe' => [
                'method' => 'GET',
                'path' => '/{0}/coe/',
            ],
        ];
    }
}
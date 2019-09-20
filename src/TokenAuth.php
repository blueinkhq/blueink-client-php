<?php
/**
 * Created by PhpStorm.
 * User: zlovelady
 * Date: 11/22/17
 * Time: 8:12 PM
 */

namespace BlueInk\ApiClient;


use Snorlax\Auth\BearerAuth;

class TokenAuth extends BearerAuth
{
    public function getAuthType() {
        return 'Token';
    }
}
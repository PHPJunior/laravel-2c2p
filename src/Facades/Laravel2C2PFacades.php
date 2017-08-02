<?php
/**
 * Created by PhpStorm.
 * User: nyinyilwin
 * Date: 8/1/17
 * Time: 7:24 PM
 */

namespace PhpJunior\Laravel2C2P\Facades;

use Illuminate\Support\Facades\Facade;

class Laravel2C2PFacades extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return '2c2p-payment-gateway-api';
    }
}
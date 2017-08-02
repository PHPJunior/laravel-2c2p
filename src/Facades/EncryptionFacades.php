<?php
/**
 * Created by PhpStorm.
 * User: nyinyilwin
 * Date: 8/1/17
 * Time: 3:47 PM
 */

namespace PhpJunior\Laravel2C2P\Facades;

use Illuminate\Support\Facades\Facade;

class EncryptionFacades extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return '2c2p-encryption';
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: kogleron
 * Date: 20/05/16
 * Time: 19:00
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TwilioLookups
 *
 * @package App\Facades
 * @see \Lookups_Services_Twilio
 */
class TwilioLookups extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TwilioLookups';
    }
}
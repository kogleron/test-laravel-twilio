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
 * Class TwilioTwiml
 *
 * @package App\Facades
 * @see \Services_Twilio_Twiml
 */
class TwilioTwiml extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TwilioTwiml';
    }
}
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
 * Class TwilioPricing
 *
 * @package App\Facades
 * @see \Pricing_Services_Twilio
 */
class TwilioPricing extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TwilioPricing';
    }
}
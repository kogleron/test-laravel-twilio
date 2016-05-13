<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TwilioAppServiceProvider extends ServiceProvider
{
    /**
     * Initializes and registers Twilio SDK's object.
     *
     * @return void
     */
    public function register()
    {

        $token      = $_ENV['TWILIO_AUTH_TOKEN'];
        $accountSid = $_ENV['TWILIO_ACCOUNT_SID'];

        $this->app->instance('Twilio',
            new \Services_Twilio($accountSid, $token)
        );
        $this->app->instance('TwilioLookups',
            new\Lookups_Services_Twilio($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN'])
        );
        $this->app->instance('TwilioPricing',
            new \Pricing_Services_Twilio($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN'])
        );
    }
}

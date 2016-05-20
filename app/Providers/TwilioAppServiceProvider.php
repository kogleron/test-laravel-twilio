<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TwilioAppServiceProvider extends ServiceProvider
{
    /**
     * @param string $baseUri
     * @param string $userAgent
     *
     * @return \Services_Twilio_HttpStream|\Services_Twilio_TinyHttp
     * @throws \Services_Twilio_HttpException
     */
    protected function getHttpClient($baseUri, $userAgent)
    {
        if (!in_array('openssl', get_loaded_extensions())) {
            throw new \Services_Twilio_HttpException("The OpenSSL extension is required but not currently enabled. " .
                "For more information, see http://php.net/manual/en/book.openssl.php");
        }
        if (in_array('curl', get_loaded_extensions())) {
            $http = new \Services_Twilio_TinyHttp(
                $baseUri,
                [
                    "curlopts" => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_USERAGENT      => $userAgent,
                        CURLOPT_HTTPHEADER     => ['Accept-Charset: utf-8'],
                    ],
                ]
            );
        } else {
            $http = new \Services_Twilio_HttpStream(
                $baseUri,
                [
                    "http_options" => [
                        "http" => [
                            "user_agent" => $userAgent,
                            "header"     => "Accept-Charset: utf-8\r\n",
                        ],
                        "ssl"  => [
                            'verify_peer'  => false,
                            'verify_depth' => 5,
                        ],
                    ],
                ]
            );
        }

        return $http;
    }

    /**
     * Initializes and registers Twilio SDK's object.
     *
     * @throws \Services_Twilio_HttpException
     */
    public function register()
    {
        $token = $_ENV['TWILIO_AUTH_TOKEN'];
        $sid   = $_ENV['TWILIO_ACCOUNT_SID'];

        $this->app->instance('Twilio',
            new \Services_Twilio($sid, $token, null,
                env('TWILIO_SSL_VERIFY_PEER', 0) ? null : $this->getHttpClient('https://api.twilio.com',
                    \Services_Twilio::qualifiedUserAgent(phpversion())))
        );
        $this->app->instance('TwilioLookups',
            new \Lookups_Services_Twilio($sid, $token, null,
                env('TWILIO_SSL_VERIFY_PEER', 0) ? null : $this->getHttpClient('https://lookups.twilio.com',
                    \Lookups_Services_Twilio::qualifiedUserAgent(phpversion())))
        );
        $this->app->instance('TwilioPricing',
            new \Pricing_Services_Twilio($sid, $token, null,
                env('TWILIO_SSL_VERIFY_PEER', 0) ? null : $this->getHttpClient('https://pricing.twilio.com',
                    \Pricing_Services_Twilio::qualifiedUserAgent(phpversion())))
        );
        $this->app->instance('TwilioTwiml',
            new \Services_Twilio_Twiml()
        );
    }
}

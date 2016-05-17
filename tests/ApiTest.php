<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{
    use DatabaseTransactions;

    public function testPhone()
    {
        $mockPhoneNumber               = Mockery::mock();
        $mockPhoneNumber->phone_number = '+12345';

        $mockLookupPhoneNumber               = Mockery::mock();
        $mockLookupPhoneNumber->country_code = 'us';

        $mockTwilioService                                  = Mockery::mock();
        $mockTwilioService->account                         = Mockery::mock();
        $mockTwilioService->account->incoming_phone_numbers = [$mockPhoneNumber];

        $mockTwilioLookupsService                = Mockery::mock();
        $mockTwilioLookupsService->phone_numbers = Mockery::mock();
        $mockTwilioLookupsService->phone_numbers->shouldReceive('get')
            ->andReturn($mockLookupPhoneNumber);

        App::instance('Twilio', $mockTwilioService);
        App::instance('TwilioLookups', $mockTwilioLookupsService);

        /**
         * Existing number
         */
        $response = $this->call('GET', 'api/phone/us');

        $this->assertJsonStringEqualsJsonString(json_encode('+12345'), $response->getContent());

        /**
         * Buy number
         */

        $mockPhoneNumber->phone_number = '+76543';

        $mockAvailablePhoneNumbers                          = Mockery::mock();
        $mockAvailablePhoneNumbers->available_phone_numbers = [$mockPhoneNumber];

        $mockTwilioService->account->incoming_phone_numbers = Mockery::mock();
        $mockTwilioService->account->incoming_phone_numbers->shouldReceive('create');

        $mockTwilioService->account->available_phone_numbers = Mockery::mock();
        $mockTwilioService->account->available_phone_numbers->shouldReceive('getList')
            ->andReturn($mockAvailablePhoneNumbers);

        $response = $this->call('GET', 'api/phone/us');

        $this->assertJsonStringEqualsJsonString(json_encode('+76543'), $response->getContent());
    }

    public function testCountries()
    {
        $mockTwilioService = Mockery::mock();

        $mockAvailablePhoneNumbers                          = Mockery::mock();
        $mockAvailablePhoneNumbers->available_phone_numbers = [1];

        $mockTwilioService->account                          = Mockery::mock();
        $mockTwilioService->account->available_phone_numbers = Mockery::mock();
        $mockTwilioService->account->available_phone_numbers->shouldReceive('getList')->andReturn($mockAvailablePhoneNumbers);

        $mockTwilioPricingService = Mockery::mock();

        $mockCountryGB              = Mockery::mock();
        $mockCountryGB->iso_country = 'gb';

        $mockCountryCA              = Mockery::mock();
        $mockCountryCA->iso_country = 'ca';

        $mockTwilioPricingService->phoneNumberCountries = [
            $mockCountryGB,
            $mockCountryCA
        ];

        App::instance('Twilio', $mockTwilioService);
        App::instance('TwilioPricing', $mockTwilioPricingService);

        $response = $this->call('GET', 'api/countries');

        $this->assertJson($response->getContent());

        $responseCountries = json_decode($response->getContent());

        sort($responseCountries);

        $this->assertEquals(['ca', 'gb', 'us'], $responseCountries);
    }

    public function testVoiceUrl()
    {
        $response = $this->call('POST', 'api/voice-url', [
            'CallSid'    => 'call-sid',
            'AccountSid' => 'account-sid',
            'From'       => 'from',
            'To'         => 'to'
        ]);

        $this->assertTrue((bool)strpos($response->getContent(), env('TWILIO_DIAL_NUMBER')));
    }

    public function testStatusCallback()
    {
        $call              = new \App\CallsLog();
        $call->call_sid    = 'call-sid-' . time();
        $call->account_sid = 'account-sid';
        $call->from        = 'from';
        $call->to          = 'to';
        $call->save();

        $this->call('POST', 'api/status-callback', [
            'CallSid'      => $call->call_sid,
            'CallDuration' => '30'
        ]);

        /** @var \App\CallsLog $call */
        $call = \App\CallsLog::find($call->id);

        $this->assertEquals('30', $call->duration);
    }

    public function testSmsUrl()
    {
        $messageSid = 'MessageSid' + time();

        $this->call('POST', 'api/sms-url', [
            'MessageSid' => $messageSid,
            'AccountSid' => 'AccountSid',
            'From'       => 'From',
            'To'         => 'To',
            'Body'       => 'Body',
        ]);

        $this->assertTrue(true, !empty(\App\MessagesLog::where('message_sid', $messageSid)->firstOrFail()));
    }
}

<?php

class PageTest extends TestCase
{
    protected function getPhone()
    {
        $mockPhoneNumber               = Mockery::mock();
        $mockPhoneNumber->phone_number = '+12345';

        $mockLookupPhoneNumber               = Mockery::mock();
        $mockLookupPhoneNumber->country_code = 'us';

        $mockTwilioService                                  = Twilio::getFacadeRoot();
        $mockTwilioService->account                         = Mockery::mock();
        $mockTwilioService->account->incoming_phone_numbers = [$mockPhoneNumber];

        $mockTwilioLookupsService                = TwilioLookups::getFacadeRoot();
        $mockTwilioLookupsService->phone_numbers = Mockery::mock();
        $mockTwilioLookupsService->phone_numbers->shouldReceive('get')
            ->andReturn($mockLookupPhoneNumber);

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

    protected function getCountries()
    {
        $mockTwilioService = Twilio::getFacadeRoot();

        $mockAvailablePhoneNumbers                          = Mockery::mock();
        $mockAvailablePhoneNumbers->available_phone_numbers = [1];

        $mockTwilioService->account                          = Mockery::mock();
        $mockTwilioService->account->available_phone_numbers = Mockery::mock();
        $mockTwilioService->account->available_phone_numbers->shouldReceive('getList')->andReturn($mockAvailablePhoneNumbers);

        $mockTwilioPricingService = TwilioPricing::getFacadeRoot();

        $mockCountryGB              = Mockery::mock();
        $mockCountryGB->iso_country = 'gb';

        $mockCountryCA              = Mockery::mock();
        $mockCountryCA->iso_country = 'ca';

        $mockTwilioPricingService->phoneNumberCountries = [
            $mockCountryGB,
            $mockCountryCA
        ];

        $response = $this->call('GET', 'api/countries');

        $this->assertJson($response->getContent());

        $responseCountries = json_decode($response->getContent());

        sort($responseCountries);

        $this->assertEquals(['ca', 'gb', 'us'], $responseCountries);
    }

    /**
     * A functional test
     *
     * @return void
     */
    public function testPage()
    {
        $this->visit('/')
            ->see('Twilio');

        /*
         * Ajax request
         */
        $this->getCountries();

        /*
         * Ajax request after clicking on a country
         */
        $this->getPhone();
    }
}

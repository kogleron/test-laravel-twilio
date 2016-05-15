<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{
    public function testCountries()
    {
        $response = $this->call('GET', 'api/countries');

        $this->assertTrue($response->isOk());
        $this->assertTrue(count(json_decode($response->getContent())) > 0);
    }

    public function testVoiceUrl()
    {
        $response = $this->call('POST', 'api/voice-url', [
            'CallSid'    => 'call-sid',
            'AccountSid' => 'account-sid',
            'From'       => 'from',
            'To'         => 'to'
        ]);

        $this->assertTrue($response->isOk());
        $this->assertTrue((bool)strpos($response->getContent(), env('TWILIO_DIAL_NUMBER')));
    }

    public function testStatusCallback()
    {
        $call              = new \App\CallsLog();
        $call->call_sid    = 'call-sid';
        $call->account_sid = 'account-sid';
        $call->from        = 'from';
        $call->to          = 'to';
        $call->save();

        $response = $this->call('POST', 'api/status-callback', [
            'CallSid'      => 'call-sid',
            'CallDuration' => '30'
        ]);

        $this->assertTrue($response->isOk());
    }

    public function testSmsUrl()
    {
        $response = $this->call('POST', 'api/sms-url', [
            'MessageSid' => 'MessageSid',
            'AccountSid' => 'AccountSid',
            'From'       => 'From',
            'To'         => 'To',
            'Body'       => 'Body',
        ]);

        $this->assertTrue($response->isOk());
    }
}

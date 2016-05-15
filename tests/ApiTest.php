<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{
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

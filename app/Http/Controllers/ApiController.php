<?php

namespace App\Http\Controllers;

use App\CallsLog;
use App\MessagesLog;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;

class ApiController extends BaseController
{
    public function postVoiceUrl()
    {
        $call              = new CallsLog();
        $call->call_sid    = Input::get('CallSid');
        $call->account_sid = Input::get('AccountSid');
        $call->from        = Input::get('From');
        $call->to          = Input::get('To');
        $call->created     = date('Y-m-d H:i:s');
        $call->save();

        $responseTwilio = new \Services_Twilio_Twiml();
        $responseTwilio->dial(env('TWILIO_DIAL_NUMBER'), ['callerId' => $call->from]);

        $response = response()->make($responseTwilio);

        $response->header('Content-Type', 'text/xml');

        return $response;
    }

    public function postStatusCallback()
    {
        /** @var CallsLog $call */
        $call           = CallsLog::where('call_sid', Input::get('CallSid'))->firstOrFail();
        $call->duration = Input::get('CallDuration');
        $call->save();
    }

    public function postSmsUrl()
    {
        $message              = new MessagesLog();
        $message->created     = date('Y-m-d H:i:s');
        $message->message_sid = Input::get('MessageSid');
        $message->account_sid = Input::get('AccountSid');
        $message->from        = Input::get('From');
        $message->to          = Input::get('To');
        $message->body        = Input::get('Body');
        $message->save();

        $responseTwilio = new \Services_Twilio_Twiml();
        $response       = response()->make($responseTwilio);

        $response->header('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * @param string $country
     *
     * @throws \Exception
     * @return \Illuminate\Http\Response
     */
    public function getPhone($country)
    {
        $country = strtolower($country);
        $twilio  = \App::make('Twilio');
        $lookups = \App::make('TwilioLookups');


        foreach ($twilio->account->incoming_phone_numbers as $number) {

            $info = $lookups->phone_numbers->get($number->phone_number);
            if ($country == strtolower($info->country_code)) {

                return response()->json($number->phone_number);
            }
        }

        $numbers = $twilio
            ->account
            ->available_phone_numbers
            ->getList(strtoupper($country), 'Local', [
                'SmsEnabled'   => 'True',
                'VoiceEnabled' => 'True'
            ])
            ->available_phone_numbers;

        foreach ($numbers as $number) {

            $twilio->account->incoming_phone_numbers->create(
                [
                    'PhoneNumber'    => $number->phone_number,
                    'VoiceMethod'    => 'POST',
                    'VoiceUrl'       => url('/api/voice-url'),
                    'StatusCallback' => url('/api/status-callback'),
                    'SmsMethod'      => 'POST',
                    'SmsUrl'         => url('/api/sms-url')
                ]
            );

            return response()->json($number->phone_number);
        }

        throw new \Exception('There is no available numbers for your country');
    }

    public function getCountries()
    {
        $twilio    = \App::make('Twilio');
        $pricing   = \App::make('TwilioPricing');
        $countries = [];
        $result    = ['us'];

        foreach ($pricing->phoneNumberCountries as $c) {

            $country = strtolower($c->iso_country);

            if ($country == 'us') {
                continue;
            }

            $countries[] = $country;
        }

        while (!empty($countries) && count($result) < env('TWILIO_COUNTRIES_NUM', 3)) {

            $country = array_splice($countries, rand(0, count($countries)), 1)[0];

            try {
                $numbers = $twilio
                    ->account
                    ->available_phone_numbers
                    ->getList(strtoupper($country), 'Local', [
                        'SmsEnabled'   => 'True',
                        'VoiceEnabled' => 'True'
                    ])
                    ->available_phone_numbers;

                if (!count($numbers)) {
                    continue;
                }

            } catch (\Exception $e) {
                continue;
            }

            $result[] = $country;
        }

        return response()->json($result);
    }
}

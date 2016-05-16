<?php

namespace App\Http\Controllers;

use App\CallsLog;
use App\MessagesLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function postVoiceUrl(Request $request)
    {
        $call              = new CallsLog();
        $call->call_sid    = $request->get('CallSid');
        $call->account_sid = $request->get('AccountSid');
        $call->from        = $request->get('From');
        $call->to          = $request->get('To');
        $call->created     = date('Y-m-d H:i:s');
        $call->save();

        /** @var \Services_Twilio_Twiml $responseTwilio */
        $responseTwilio = \App::make('TwilioTwiml');
        $responseTwilio->dial(env('TWILIO_DIAL_NUMBER'), ['callerId' => $call->from]);

        $response = response()->make($responseTwilio);

        $response->header('Content-Type', 'text/xml');

        return $response;
    }

    public function postStatusCallback(Request $request)
    {
        /** @var CallsLog $call */
        $call           = CallsLog::where('call_sid', $request->get('CallSid'))->firstOrFail();
        $call->duration = $request->get('CallDuration');
        $call->save();
    }

    public function postSmsUrl(Request $request)
    {
        $message              = new MessagesLog();
        $message->created     = date('Y-m-d H:i:s');
        $message->message_sid = $request->get('MessageSid');
        $message->account_sid = $request->get('AccountSid');
        $message->from        = $request->get('From');
        $message->to          = $request->get('To');
        $message->body        = $request->get('Body');
        $message->save();

        /** @var \Services_Twilio_Twiml $responseTwilio */
        $responseTwilio = \App::make('TwilioTwiml');
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
        /** @var \Services_Twilio $twilio */
        /** @var \Lookups_Services_Twilio $lookups */

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
        /** @var \Services_Twilio $twilio */
        /** @var \Pricing_Services_Twilio $pricing */
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

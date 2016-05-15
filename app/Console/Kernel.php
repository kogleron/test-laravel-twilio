<?php

namespace App\Console;

use App\CallsLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $calls = CallsLog::whereNull('thanked')
                ->whereNotNull('duration')
                ->where('created', '<=', date('Y-m-d H:i:s', time() - env('TWILIO_THANKS_IN', 18) * 60))
                ->get();

            if (!count($calls)) {
                return;
            }

            /** @var \Services_Twilio $twilio */
            $twilio     = \App::make('Twilio');
            $exceptions = [];

            foreach ($calls as $call) {

                /** @var CallsLog $call */

                if ($call->duration > env('TWILIO_THANKS_SHADE', 2)) {
                    $message = '120 more';
                } else {
                    $message = '120 less';
                }

                print "\n" . $call->id . ' - ' . $message;

                try {
                    $twilio->account->messages->sendMessage(
                        $call->to,
                        $call->from,
                        $message
                    );
                } catch (\Exception $e) {

                    $exceptions[] = $e->getMessage();
                }

                $call->thanked = date('Y-m-d H:i:s');
                $call->save();
            }

            if (count($exceptions)) {

                throw new \Exception(implode("\n", $exceptions));
            }
        })->everyMinute();
    }
}

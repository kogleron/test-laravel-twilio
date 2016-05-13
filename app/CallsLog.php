<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CallsLog
 *
 * @package App
 *
 * @property int    $id
 * @property string $call_sid
 * @property string $account_sid
 * @property string $from
 * @property string $to
 * @property string $created
 * @property int    $duration
 * @property string $thanked
 */
class CallsLog extends Model
{
    protected $table      = 'calls_log';
    public    $timestamps = false;
}

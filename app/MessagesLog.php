<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MessagesLog
 *
 * @package App
 *
 * @property int    $id
 * @property string $created
 * @property string $message_sid
 * @property string $account_sid
 * @property string $from
 * @property string $to
 * @property string $body
 */
class MessagesLog extends Model
{
    protected $table      = 'messages_log';
    public    $timestamps = false;
}

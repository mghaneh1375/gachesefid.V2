<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Message'
 *
 * @property integer $id
 * @property integer $senderId
 * @property integer $receiverId
 * @property integer $status
 * @property string $message
 * @property string $subject
 * @property string $date
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Message whereSenderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Message whereReceiverId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Message whereStatus($value)
 */

class Message extends Model {

    protected $table = 'messages';
    public $timestamps = false;

    public static function whereId($target) {
        return Message::find($target);
    }
}

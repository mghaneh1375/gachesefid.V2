<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Activation'
 *
 * @property integer $id
 * @property integer $code
 * @property string $sendTime
 * @property string $phoneNum
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Activation wherePhoneNum($value)
 */

class Activation extends Model {

    protected $table = 'activation';
    public $timestamps = false;

}
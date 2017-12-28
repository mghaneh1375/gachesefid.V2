<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'OffCode'
 *
 * @property integer $id
 * @property string $code
 * @property string $expireTime
 * @property int $type
 * @property int $amount
 * @method static \Illuminate\Database\Query\Builder|\App\models\OffCode whereCode($value)
 * @mixin \Eloquent
 */

class OffCode extends Model{

    protected $table = 'offCode';
    public $timestamps = false;

}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'OrderId'
 *
 * @property integer $id
 * @property string $code
 * @method static \Illuminate\Database\Query\Builder|\App\models\OrderId whereCode($value)
 * @mixin \Eloquent
 */

class OrderId extends Model{

    protected $table = 'orderId';
    public $timestamps = false;

}
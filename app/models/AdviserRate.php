<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'AdviserRate'
 *
 * @property integer $id
 * @property integer $adviserId
 * @property integer $uId
 * @property integer $rate
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\AdviserRate whereUId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\AdviserRate whereAdviserId($value)
 */

class AdviserRate extends Model {

    protected $table = 'adviserRate';
    public $timestamps = false;

}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'RedundantInfo2'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $kindSchool
 * @property string $homePhone
 * @property string $fatherPhone
 * @property string $motherPhone
 * @property string $address
 * @property string $homePostCode
 * @method static \Illuminate\Database\Query\Builder|\App\models\RedundantInfo2 whereUId($value)
 * @mixin \Eloquent
 */

class RedundantInfo2 extends Model {

    protected $table = 'redundantInfo2';
    public $timestamps = false;
    
    
}
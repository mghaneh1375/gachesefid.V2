<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'SchoolStudent'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $sId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\SchoolStudent whereUId($value)
 */

class SchoolStudent extends Model{

    protected $table = 'schoolStudent';
    public $timestamps = false;

}
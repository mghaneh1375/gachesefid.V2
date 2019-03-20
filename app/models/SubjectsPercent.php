<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'SubjectsPercent'
 *
 * @property integer $id
 * @property integer $sId
 * @property integer $qId
 * @property integer $uId
 * @property integer $percent
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\SubjectsPercent whereQId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\SubjectsPercent whereSId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\SubjectsPercent whereUId($value)
 */

class SubjectsPercent extends Model {

    protected $table = 'subjectsPercent';
    public $timestamps = false;
}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'RedundantInfo1'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $cityId
 * @property integer $gradeId
 * @property string $email
 * @property string $schoolName
 * @property string $fatherName
 * @property string $NID
 * @method static \Illuminate\Database\Query\Builder|\App\models\RedundantInfo1 whereUId($value)
 * @mixin \Eloquent
 */

class RedundantInfo1 extends Model {

    protected $table = 'redundantInfo1';
    public $timestamps = false;
}
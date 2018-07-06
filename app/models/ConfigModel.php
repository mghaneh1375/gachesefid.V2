<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'ConfigModel'
 *
 * @property integer $id
 * @property boolean $percentOfPackage
 * @property boolean $percentOfQuizes
 * @property boolean $advisorPercent
 * @property boolean $questionMin
 * @property boolean $rankInQuiz
 * @property boolean $likeMin
 * @property boolean $makeQuestionMin
 * @property boolean $moneyMin
 */



class ConfigModel extends Model {

    protected $table = 'config';
    public $timestamps = false;

}
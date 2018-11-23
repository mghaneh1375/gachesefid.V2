<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'ROQ2'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $quizId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ2 whereUId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ2 whereQuizId($value)
 */

class ROQ2 extends Model {

    protected $table = 'ROQ2';
    public $timestamps = false;

    public static function whereId($target) {
        return ROQ2::find($target);
    }

}
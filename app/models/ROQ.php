<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'ROQ'
 *
 * @property integer $id
 * @property integer $quizId
 * @property integer $questionId
 * @property integer $uId
 * @property float $result
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ whereQuestionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ whereQuizId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ROQ whereUId($value)
 */

class ROQ extends Model {

    protected $table = 'ROQ';
    public $timestamps = false;

    public static function whereId($target) {
        return ROQ::find($target);
    }

}
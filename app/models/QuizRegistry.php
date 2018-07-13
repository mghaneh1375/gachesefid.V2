<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'QuizRegistry'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $qId
 * @property integer $quizMode
 * @property string $timeEntry
 * @property boolean $online
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\QuizRegistry whereUId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\QuizRegistry whereQuizMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\QuizRegistry whereQId($value)
 */

class QuizRegistry extends Model {

    protected $table = 'quizRegistry';
    public $timestamps = false;

    public static function whereId($target) {
        return QuizRegistry::find($target);
    }
}
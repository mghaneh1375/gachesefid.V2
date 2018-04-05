<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'ComposeQuizItem'
 *
 * @property integer $id
 * @property integer $quizId
 * @property integer $quizMode
 * @property integer $composeId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\ComposeQuizItem whereComposeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ComposeQuizItem whereQuizId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\ComposeQuizItem whereQuizMode($value)
 */

class ComposeQuizItem extends Model {

    protected $table = 'composeQuizItem';
    public $timestamps = false;

    public static function whereId($target) {
        return ComposeQuizItem::find($target);
    }

}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'ComposeQuiz'
 *
 * @property integer $id
 * @property string $name
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\ComposeQuiz whereName($value)
 */

class ComposeQuiz extends Model {

    protected $table = 'composeQuiz';
    public $timestamps = false;

    public static function whereId($target) {
        return ComposeQuiz::find($target);
    }

}
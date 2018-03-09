<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'QuizStatus'
 *
 * @property integer $id
 * @property int $floor
 * @property int $ceil
 * @property int $level
 * @property int $type
 * @property boolean $pic
 * @property string $color
 * @property string $status
 * @method static \Illuminate\Database\Query\Builder|\App\models\QuizStatus whereLevel($value)
 * @mixin \Eloquent
 */

class QuizStatus extends Model {

    protected $table = 'quizStatus';
    public $timestamps = false;

    public function subject() {
//        return $this->belongsTo('Subject', 'sId', 'id');
        return $this->hasMany('Subject', 'id', 'sId');
    }

    public static function whereId($target) {
        return QuizStatus::find($target);
    }

}
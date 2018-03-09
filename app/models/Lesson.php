<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Lesson'
 *
 * @property integer $id
 * @property string $name
 * @property integer $gradeId
 * @property integer $coherenece
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Lesson whereGradeId($value)
 */

class Lesson extends Model{

    protected $table = 'lesson';
    public $timestamps = false;

    public function grade() {
        return $this->belongsTo('Grade', 'gradeId', 'id');
    }
//
//    public function subjects() {
//        return $this->hasMany('Subject', 'lId', 'id');
//    }

    public static function whereId($value) {
        return Lesson::find($value);
    }

}
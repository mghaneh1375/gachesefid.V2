<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

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
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model {

    protected $table = 'grade';
    public $timestamps = false;

    public function lessons() {
        return $this->hasMany('Lesson', 'did', 'id');
    }

    public static function whereId($target) {
        return Grade::find($target);
    }

}
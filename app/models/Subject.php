<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model {

    protected $table = 'subject';
    public $timestamps = false;

    public static function whereName($name) {
        return Subject::where('name', '=', $name)->first();
    }

    public function lessons() {
        return $this->hasMany('Lesson', 'id', 'lId');
    }

    public function questions() {
        return $this->hasMany('Question', 'sId', 'id');
    }

}
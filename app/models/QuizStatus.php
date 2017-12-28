<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class QuizStatus extends Model {

    protected $table = 'quizStatus';
    public $timestamps = false;

    public function subject() {
//        return $this->belongsTo('Subject', 'sId', 'id');
        return $this->hasMany('Subject', 'id', 'sId');
    }

}
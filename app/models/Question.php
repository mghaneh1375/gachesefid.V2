<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model {

    protected $table = 'question';
    public $timestamps = false;

    public function scopeAccepted($query) {
        return $query->where('status', '=', 1);
    }

//    public function subject() {
//        return $this->belongsTo('Subject', 'sId', 'id');
//        return $this->hasMany('Subject', 'id', 'sId');
//    }

    public static function whereId($value) {
        return Question::find($value);
    }

}
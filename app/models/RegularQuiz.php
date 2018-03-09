<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class RegularQuiz extends Model {

    protected $table = 'regularQuiz';
    public $timestamps = false;

    public static function whereId($target) {
        return RegularQuiz::find($target);
    }

}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model {

    protected $table = 'discussion';
    public $timestamps = false;

    public static function whereId($target) {
        return Discussion::find($target);
    }

}
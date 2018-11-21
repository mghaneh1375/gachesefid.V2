<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class ROQ2 extends Model {

    protected $table = 'ROQ2';
    public $timestamps = false;

    public static function whereId($target) {
        return ROQ2::find($target);
    }

}
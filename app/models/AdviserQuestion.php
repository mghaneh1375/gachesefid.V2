<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'AdviserQuestion'
 *
 * @property integer $id
 * @property string $name
 */

class AdviserQuestion extends Model {

    protected $table = 'adviserQuestion';
    public $timestamps = false;

    public static function whereId($target) {
        return AdviserQuestion::find($target);
    }

}
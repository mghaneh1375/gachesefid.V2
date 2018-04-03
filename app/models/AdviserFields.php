<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'AdviserFields'
 *
 * @property integer $id
 * @property integer $uId
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\models\AdviserFields whereUID($value)
 */

class AdviserFields extends Model {

    protected $table = 'adviserFields';
    public $timestamps = false;

    public static function whereId($target) {
        return AdviserFields::find($target);
    }

}
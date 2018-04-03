<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'AdviserInfo'
 *
 * @property integer $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\models\AdviserInfo whereUID($value)
 */

class AdviserInfo extends Model {

    protected $table = 'adviserInfo';
    public $timestamps = false;

    public static function whereId($target) {
        return AdviserInfo::find($target);
    }

}
<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'School'
 *
 * @property integer $id
 * @property string $name
 * @property int $level
 * @property int $uId
 * @property int $kind
 * @property int $cityId
 * @method static \Illuminate\Database\Query\Builder|\App\models\School whereUId($value)
 * @mixin \Eloquent
 */

class School extends Model{

    protected $table = 'school';
    public $timestamps = false;

    public static function whereId($target) {
        return School::find($target);
    }

    public function user() {
        return $this->hasOne('App\models\User', 'uId');
    }

    public function school() {
        return $this->hasOne('App\models\School', 'sId', 'uId');
    }

}
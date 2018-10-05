<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'AdviserInfo'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $cityId
 * @property integer $field
 * @property integer $workYears
 * @property integer $birthDay
 * @property integer $lastCertificate
 * @property string $schools
 * @property string $essay
 * @property string $honors
 * @method static \Illuminate\Database\Query\Builder|\App\models\AdviserInfo whereUID($value)
 */

class AdviserInfo extends Model {

    protected $table = 'adviserInfo';
    public $timestamps = false;

    public static function whereId($target) {
        return AdviserInfo::find($target);
    }

}
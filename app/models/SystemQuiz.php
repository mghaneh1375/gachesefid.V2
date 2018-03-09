<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'SystemQuiz'
 *
 * @property integer $id
 * @property integer $price
 * @property string $name
 * @property string $startReg
 * @property string $endReg
 * @property string $startDate
 * @property string $startTime
 *
 * @mixin \Eloquent
 */

class SystemQuiz extends Model {

    protected $table = 'systemQuiz';
    public $timestamps = false;

    public static function whereId($target) {
        return SystemQuiz::find($target);
    }

}
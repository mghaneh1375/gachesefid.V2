<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Enheraf'
 *
 * @property integer $id
 * @property integer $lId
 * @property integer $qId
 * @property integer $val
 * @property float $lessonAVG
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Enheraf whereLId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Enheraf whereQId($value)
 */

class Enheraf extends Model {

    protected $table = 'enheraf';
    public $timestamps = false;

    public static function whereId($target) {
        return Enheraf::find($target);
    }

}
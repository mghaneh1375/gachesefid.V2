<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'RegularQOQ'
 *
 * @property integer $id
 * @property integer $quizId
 * @property integer $questionId
 * @property integer $qNo
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\RegularQOQ whereQuizId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\RegularQOQ whereQNo($value)
 */


class RegularQOQ extends Model {

    protected $table = 'regularQOQ';
    public $timestamps = false;
}
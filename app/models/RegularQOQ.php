<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'RegularQOQ'
 *
 * @property integer $id
 * @property integer $quizId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\RegularQOQ whereQuizId($value)
 */


class RegularQOQ extends Model {

    protected $table = 'regularQOQ';
    public $timestamps = false;
}
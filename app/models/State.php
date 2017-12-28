<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'State'
 *
 * @property integer $id
 * @property string $name
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\State whereStateName($value)
 */

class State extends Model {

    protected $table = 'state';
    public $timestamps = false;
}
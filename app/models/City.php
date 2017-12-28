<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'City'
 *
 * @property integer $id
 * @property integer $stateId
 * @property string $name
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\City whereStateId($value)
 */

class City extends Model {

    protected $table = 'city';
    public $timestamps = false;

}
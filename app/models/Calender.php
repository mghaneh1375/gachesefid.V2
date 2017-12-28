<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;


/**
 * An Eloquent Model: 'Calender'
 *
 * @property integer $id
 * @property string $date
 * @property string $event
 * @mixin \Eloquent
 */

class Calender extends Model {

    protected $table = 'calender';
    public $timestamps = false;

}
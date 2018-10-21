<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'School'
 *
 * @property integer $id
 * @property int $nId
 * @property int $sId
 * @method static \Illuminate\Database\Query\Builder|\App\models\NamayandeSchool whereSId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\NamayandeSchool whereNId($value)
 * @mixin \Eloquent
 */

class NamayandeSchool extends Model{

    protected $table = 'namayandeSchool';
    public $timestamps = false;

}
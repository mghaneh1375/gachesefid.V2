<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Taraz'
 *
 * @property integer $id
 * @property int $percent
 * @property int $lId
 * @property int $taraz
 * @property int $qEntryId
 * @method static \Illuminate\Database\Query\Builder|\App\models\Taraz whereQEntryId($value)
 * @mixin \Eloquent
 */

class Taraz extends Model {

    protected $table = 'taraz';
    public $timestamps = false;

}
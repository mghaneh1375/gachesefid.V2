<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'LOK'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $questionId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\LOK whereQuestionId($value)
 */

class LOK extends Model {

    protected $table = 'LOK';
    public $timestamps = false;
}

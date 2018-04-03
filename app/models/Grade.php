<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Grade'
 *
 * @property integer $id
 * @property integer $field
 * @property string $name
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Grade whereField($value)
 */

class Grade extends Model {

    protected $table = 'grade';
    public $timestamps = false;

    public function lessons() {
        return $this->hasMany('Lesson', 'did', 'id');
    }

    public static function whereId($target) {
        return Grade::find($target);
    }

}
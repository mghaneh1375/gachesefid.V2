<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Question'
 *
 * @property integer $id
 * @property integer $organizationId
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Question whereOrganizationId($value)
 */

class Question extends Model {

    protected $table = 'question';
    public $timestamps = false;

    public function scopeAccepted($query) {
        return $query->where('status', '=', 1);
    }

//    public function subject() {
//        return $this->belongsTo('Subject', 'sId', 'id');
//        return $this->hasMany('Subject', 'id', 'sId');
//    }

    public static function whereId($value) {
        return Question::find($value);
    }

}
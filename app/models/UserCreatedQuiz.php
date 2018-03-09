<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'UserCreatedQuiz'
 *
 * @property integer $id
 * @property integer $uId
 * @property integer $toPay
 * @property boolean $statue
 * @property string $timeEntry
 * @property string $created
 *
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\UserCreatedQuiz whereUId($value)
 */

class UserCreatedQuiz extends Model {

    protected $table = 'userCreatedQuiz';
    public $timestamps = false;

    public function questions() {
        return $this->hasMany('SoldQuestion', 'quizId', 'id');
    }

    public function totalQuestions($uId) {
        $result = DB::select("select count(*) as countNum from userCreatedQuiz u, soldQuestion s WHERE u.id = s.quizId and u.uId = " . $uId);

        if($result == null || count($result) == 0 || empty($result[0]->countNum))
            return 0;

        return $result[0]->countNum;

    }

    public static function whereId($target) {
        return UserCreatedQuiz::find($target);
    }

}
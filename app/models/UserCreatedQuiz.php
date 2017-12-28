<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

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

}
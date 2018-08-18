<?php

namespace App\Http\Controllers;

use App\models\OffCode;
use App\models\QuizRegistry;
use App\models\Transaction;
use App\models\User;
use App\models\UserCreatedQuiz;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function charge($amount, $uId, $kindTransactionId, $kindMoney) {

    try{
        DB::transaction(function () use ($amount, $uId, $kindTransactionId, $kindMoney){

            $transaction = new Transaction();
            $transaction->amount = $amount;
            $transaction->userId = $uId;
            $transaction->kindMoney = $kindMoney;
            $transaction->kindTransactionId = $kindTransactionId;
            $transaction->date = getToday()["date"];
            $transaction->save();

            $user = User::whereId($uId);
            $user->money = $user->money + $amount;
            $user->save();
        });
    }
    catch (Exception $x) {
        echo $x->getMessage();
    }

}

function chargeOnline($amount, $uId, $kindTransactionId, $kindMoney, $gift, $additional) {

    try{
        DB::transaction(function () use ($amount, $uId, $kindTransactionId, $kindMoney, $gift, $additional){

            $transaction = new Transaction();
            $transaction->amount = -$amount;
            $transaction->userId = $uId;
            $transaction->kindMoney = $kindMoney;
            $transaction->kindTransactionId = $kindTransactionId;
            $transaction->date = getToday()["date"];
            $transaction->save();

            $user = User::whereId($uId);
            $user->money = $user->money + $additional;
            $user->save();

            if($gift)
                OffCode::whereCode($gift)->delete();
        });
    }
    catch (Exception $x) {
        echo $x->getMessage();
    }

}

function chargeWithGift($code, $uId, $kindTransactionId, $kindMoney) {
    try{
        DB::transaction(function () use ($code, $uId, $kindTransactionId, $kindMoney){

            $transaction = new Transaction();
            $offCode = OffCode::whereCode($code)->first();
            $transaction->amount = $offCode->amount;
            $transaction->userId = $uId;
            $transaction->kindMoney = $kindMoney;
            $transaction->kindTransactionId = $kindTransactionId;
            $transaction->date = getToday()["date"];
            $transaction->save();

            $user = User::whereId($uId);
            $user->money = $user->money + $offCode->amount;
            $user->save();

            $offCode->delete();
            
        });
    }
    catch (Exception $x) {
        echo $x->getMessage();
    }
}

function deleteRedundantOffCodes() {

    OffCode::where('expireTime', '<', date("Y/m/d"))->delete();

}

function checkOffCodeValidation($code) {

    $offCode = OffCode::whereCode($code)->first();

    if($offCode == null)
        return false;

    return ($offCode->expireTime >= date("Y-m-d"));

}

function quizRegistry($kindTransactionId, $quizMode, $amount, $uId, $kindMoney, $quizId, $useGift, $mode = true) {

    try{
        DB::transaction(function () use ($amount, $uId, $kindTransactionId, $kindMoney, $quizId, $quizMode, $useGift, $mode){

            $transaction = new Transaction();
            $transaction->amount = $amount;
            $transaction->userId = $uId;
            $transaction->kindMoney = $kindMoney;
            $transaction->kindTransactionId = $kindTransactionId;
            $transaction->date = getToday()["date"];
            $transaction->save();

            $user = User::whereId($uId);
            $user->money = $user->money - $amount;
            $user->save();

            if($mode) {
                $quizRegistry = new QuizRegistry();
                $quizRegistry->qId = $quizId;
                $quizRegistry->uId = $uId;
                $quizRegistry->quizMode = $quizMode;
                $quizRegistry->save();
            }
            else {
                $tmp = UserCreatedQuiz::whereId($quizId);
                $tmp->status = 1;
                $tmp->save();
            }

            if($useGift)
                OffCode::whereCode($useGift)->delete();
        });
    }
    catch (Exception $x) {
        echo $x->getMessage();
    }
}

function quizRegistryOnline($kindTransactionId, $quizMode, $amount, $uId, $kindMoney, $quizId, $useGift, $mode = true, $addTransaction = true) {

    try{
        DB::transaction(function () use ($amount, $uId, $kindTransactionId, $kindMoney, $quizId, $quizMode, $useGift, $mode, $addTransaction){

            if($addTransaction) {
                $transaction = new Transaction();
                $transaction->amount = -$amount;
                $transaction->userId = $uId;
                $transaction->kindMoney = $kindMoney;
                $transaction->kindTransactionId = $kindTransactionId;
                $transaction->date = getToday()["date"];
                $transaction->save();

                $user = User::whereId($uId);
                $user->money = 0;
                $user->save();
            }

            if($mode) {
                $quizRegistry = new QuizRegistry();
                $quizRegistry->qId = $quizId;
                $quizRegistry->uId = $uId;
                $quizRegistry->quizMode = $quizMode;
                $quizRegistry->save();
            }
            else {
                $tmp = UserCreatedQuiz::whereId($quizId);
                $tmp->status = 1;
                $tmp->save();
            }

            if($useGift)
                OffCode::whereCode($useGift)->delete();
        });
    }
    catch (Exception $x) {
        echo $x->getMessage();
    }
}

function checkOffCodeType($code, $mode) {

    $offCode = OffCode::whereCode($code)->first();

    if($offCode == null)
        return false;

    return ($offCode->type == $mode);
}

function getMoneyKind1() {

    $uId = Auth::user()->id;
    $amount = DB::select("select sum(amount) as sumAmount from transaction WHERE userId = " . $uId . " and kindMoney = 1");

    if($amount == null || count($amount) == 0 || $amount[0]->sumAmount == null)
        return 0;

    return $amount[0]->sumAmount;
}

function getMoneyKind2() {

}

function getTotalMoney() {

    $uId = Auth::user()->id;
    $amount = DB::select("select money from users WHERE id = " . $uId);

    if($amount == null || count($amount) == 0 || $amount[0]->money == null)
        return 0;
    return $amount[0]->money;
}
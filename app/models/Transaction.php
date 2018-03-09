<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'Transaction'
 *
 * @property integer $id
 * @property integer $kindMoney
 * @property integer $kindTransactionId
 * @property integer $amount
 * @property integer $userId
 * @property string $date
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereKindTransactionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereKindMoney($value)
 * @method static \Illuminate\Database\Query\Builder|\App\models\Transaction whereUserId($value)
 */

class Transaction extends Model {

    protected $table = 'transaction';
    public $timestamps = false;

}
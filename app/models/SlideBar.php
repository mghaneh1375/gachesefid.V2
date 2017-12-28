<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'SlideBar'
 *
 * @property integer $id
 * @property string $pic
 * @property int $link
 * @method static \Illuminate\Database\Query\Builder|\App\models\SlideBar wherePic($value)
 * @mixin \Eloquent
 */

class SlideBar extends Model {

    protected $table = 'slideBar';
    public $timestamps = false;
}
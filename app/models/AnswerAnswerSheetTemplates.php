<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class AnswerAnswerSheetTemplates extends Model {


    protected $table = 'answer_answer_sheet_template';
    public $timestamps = false;

    protected $fillable = ['name', 'row_count', 'column_count'];
}

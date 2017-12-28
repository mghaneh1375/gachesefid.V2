<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class AnswerSheetTemplates extends Model {

    protected $table = 'answer_sheet_templates';
    public $timestamps = false;

    protected $fillable = ['name', 'row_count', 'column_count'];
}

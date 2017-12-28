<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;

class AnswerTemplate extends Model {

    protected $table = 'answer_templates';
    public $timestamps = false;
    
    protected $fillable = ['row', 'column'];

    public function answer_sheet_template()
    {
        return $this->belongsToMany(Answer_sheet_template::class, 'answer_answer_sheet_template', 'answer_sheet_template_id', 'answer_template_id')
            ->withPivot('answer_number');
    }
    
}

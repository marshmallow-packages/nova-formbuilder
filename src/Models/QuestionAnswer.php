<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionAnswer extends Model
{
    use SoftDeletes;

    protected $table = 'nova_formbuilder_question_answers';

    protected $guarded = [];

    protected $casts = [
        'first_answered_at' => 'datetime',
        'answer_info' => 'json',
    ];

    protected $with = ['question'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function answer_option()
    {
        return $this->belongsTo(QuestionAnswerOption::class, 'answer_option_id', 'id');
    }

    public function form_submission()
    {
        return $this->belongsTo(FormSubmission::class);
    }
}

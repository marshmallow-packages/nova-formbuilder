<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marshmallow\NovaFormbuilder\Models\Question;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Marshmallow\NovaFormbuilder\Models\QuestionAnswerOption;

class QuestionAnswer extends Model
{
    use SoftDeletes;

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

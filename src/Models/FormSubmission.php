<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Marshmallow\NovaFormbuilder\Models\Form;
use Marshmallow\NovaFormbuilder\Models\Step;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marshmallow\NovaFormbuilder\Models\Question;
use Marshmallow\NovaFormbuilder\Models\QuestionAnswer;

class FormSubmission extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'submitted_at' => 'datetime'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($submission) {
            if (!$submission->title) {
                $submission->title = $submission->getTitle();
                $submission->saveQuietly();
            }
        });
    }

    public function getTitle()
    {
        $title = "Form #{$this->id}: ";
        if ($this->formable) {

            $formable_title = $this->formable->title ?? null;
            if (!$formable_title && $this->formable->full_name) {
                $formable_title = $this->formable->full_name;
            } elseif (!$formable_title && $this->formable->name) {
                $formable_title = $this->formable->name;
            } elseif (!$formable_title && $this->formable->email) {
                $formable_title = $this->formable->email;
            }

            return $title . "{$this->form->name} - {$formable_title}";
        }
    }

    public function formable()
    {
        return $this->morphTo();
    }

    public function getAnswersAttribute()
    {
        $answers = $this->question_answers->groupBy('question_key')->mapWithKeys(function ($answer) {
            $question_key = $answer->first()->question_key;
            $question_type = $answer->first()->question->type;
            if (count($answer) == 1 && $question_type != 'checkbox') {
                $answer = $answer->first();
                if ($answer->answer_option_id) {
                    $answer = $answer->answer_value;
                } else {
                    $answer = $answer->answer_value;
                }
            } else {
                $answer = $answer->pluck('answer_value')->toArray();
                if (count($answer) == 1) {
                    $answer = $answer[array_key_first($answer)];
                }
            }
            return [$question_key => $answer];
        })->toArray();

        return (object) $answers;
    }


    public function getFromAnswers($value)
    {
        return $this->question_answers->where('answer_key', $value)->first()->answer_value;
    }


    public function getMainImage()
    {
        if ($image = $this->getFirstMediaUrl('form_image')) {
            return $image;
        }

        return '';
    }

    public function updateAnswers($input, Step $step)
    {
        $data = self::createFormSubmissionData($input);
        $model_data = $data['model'] ?? [];
        $answer_data = $data['answers'] ?? [];

        if ($model_data && !empty($model_data)) {
            $this->update($model_data);
        }

        if (!$answer_data || empty($answer_data)) {
            return;
        }

        $this->updateAnswerData($answer_data, $step);
    }

    public function updateAnswerData($answer_data, Step $step)
    {
        $stepQuestions = $step->questions;

        collect($answer_data)->each(function ($answer, $question_key) use ($stepQuestions) {
            $question = $stepQuestions->where('name', $question_key)->first();

            if (!$question || is_null($answer)) {
                return;
            }

            if (in_array($question_key, ['photo', 'photos'])) {
                return;
            }

            if (is_array($answer) && count($answer) > 0) {
                $this->saveMultipleAnswers($answer, $question_key, $question);
            } else {
                $this->saveAnswers($answer, $question_key, $question);
            }
        });
    }

    public function saveMultipleAnswers($answers, $question_key, $question)
    {
        $question_answers = QuestionAnswer::where([
            'form_submission_id' => $this->id,
            'question_id' => $question->id,
        ])->get();

        $answers = collect($answers)->reject(function ($answer, $key) {
            return $answer === false ? true : false;
        })->toArray();

        $question_answers = $question_answers->reject(function ($question_answer) use (&$answers) {
            $in_array = in_array($question_answer->answer_key, $answers);
            if ($in_array) {
                unset($answers[$question_answer->answer_key]);
                return true;
            } else {
                $question_answer->delete();
            }
        });

        collect($answers)->each(function ($answer) use ($question_key, $question) {
            $this->saveAnswers($answer, $question_key, $question, true);
        });
    }

    public function saveAnswers($answer, $question_key, $question, $multipe = false)
    {
        $find = [
            'form_submission_id' => $this->id,
            'question_id' => $question->id,
        ];

        if ($multipe) {
            $find['answer_key'] = $answer;
        }

        $question_answer = QuestionAnswer::firstOrNew($find);

        $question_answer->question_key = $question_key;
        $question_answer->answer_key = $question_key;
        $question_answer->answer_value = $answer;

        if (is_array($question_answer->answer_value) && empty($question_answer->answer_value)) {
            return;
        }

        if ($question->has_options) {
            $options = $question->question_answer_options;

            $option = $options->where('key', $answer)->first();

            if ($option) {
                if (is_array($option->value)) {
                    $option->value = null;
                }
                $question_answer->answer_option_id = $option->id;
                $question_answer->answer_key = $option->key;
                $question_answer->answer_value = $option->value;
            }
        }

        $dirty = $question_answer->isDirty();

        if ($dirty) {
            $question_answer->save();
        }
    }

    public function getAnswerKeys()
    {
        return [];
    }

    public function getAnswers()
    {
        $keys = $this->getAnswerKeys();

        $model_data = $this->toArray();

        $default_answers = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $model_data)) {
                $default_answers[$key] = $model_data[$key];
            }
        }

        $answers = $this->question_answers->groupBy('question_key')->mapWithKeys(function ($answer) {
            $question_key = $answer->first()->question_key;
            $question_type = $answer->first()->question->type;
            if (count($answer) == 1 && $question_type != 'checkbox') {
                $answer = $answer->first();
                if ($answer->answer_option_id) {
                    $answer = $answer->answer_key;
                } else {
                    $answer = $answer->answer_value;
                }
            } else {
                $answer = $answer->pluck('answer_key', 'answer_key')->toArray();
            }
            return [$question_key => $answer];
        });

        if ($answers) {
            $answers = $answers->toArray();
        } else {
            return;
        }

        $all_answers = array_merge($default_answers, $answers);
        unset($all_answers['question_answers']);
        return $all_answers;
    }

    public function createFormSubmissionData($input)
    {
        $data_array = [];

        $keys = $this->getAnswerKeys();

        $model_array = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $input)) {
                $model_array[$key] = $input[$key];
                unset($input[$key]);
            }
        }
        $data_array['model'] = $model_array;
        $data_array['answers'] = $input;

        return $data_array;
    }

    public function question_answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Form::class, 'id', 'form_id', 'form_id', 'id');
    }
}

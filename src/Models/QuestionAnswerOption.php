<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marshmallow\NovaFormbuilder\Models\Question;

class QuestionAnswerOption extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    protected $table = 'nova_formbuilder_question_answer_options';

    protected $guarded = [];

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => false,
        'sort_on_has_many' => true,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($question_option) {
            $question_option->key = $question_option->generateKey();
            $question_option->order_column = $question_option->order_column ?? $question_option->id;
        });

        static::updating(function ($question_option) {
            if ($question_option->isDirty('value')) {
                $question_option->key = $question_option->generateKey();
            }
        });
    }

    public function generateKey()
    {
        $value = $this->value;
        $key = Str::of($value)->slug()->replace('-', '_')->toString();
        $key_count = self::whereNot('id', $this->id)->where('value', 'like', $key . "%")->count();
        if ($key_count > 0) {
            $key = "{$key}_{$key_count}";
        }

        return $key;
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

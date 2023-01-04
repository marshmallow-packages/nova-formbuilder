<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Marshmallow\NovaFormbuilder\Models\Form;
use Marshmallow\NovaFormbuilder\Models\Step;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Marshmallow\Nova\Flexible\Concerns\HasFlexible;
use Marshmallow\NovaFormbuilder\Models\QuestionAnswer;
use Marshmallow\NovaFormbuilder\Enums\QuestionFieldMap;
use Marshmallow\NovaFormbuilder\Models\QuestionAnswerOption;

class Question extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;
    use HasFlexible;

    use CascadeSoftDeletes;

    protected $table = 'nova_formbuilder_questions';

    protected $cascadeDeletes = ['question_answer_options'];

    protected $guarded = [];

    protected $with = ['step'];
    // protected $with = ['step', 'form', 'question_answer_options'];

    public $sortable = [
        'sort_when_creating' => true,
        'sort_on_has_many' => true,
    ];

    public $media_questions = [
        'photo' => 'form_image',
        'photos' => 'form_images',
    ];

    protected $casts = [
        'field_map' => QuestionFieldMap::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            if (!$question->name) {
                $question->name = $question->generateKey();
            }
        });

        static::updating(function ($question) {
            if ($question->isDirty('label') || $question->isDirty('placeholder')) {
                $question->name = $question->generateKey();
            }
        });
    }

    public function generateKey()
    {
        $label = $this->label;
        if (!$label) {
            $label = $this->placeholder;
        }

        $key = Str::of($label)->slug()->replace('-', '_')->toString();
        $key_count = self::whereNot('id', $this->id)->where('name', 'like', "%" . $key . "%")->count();
        if ($key_count > 0) {
            $key = "{$key}_{$key_count}";
        }

        return $key;
    }

    public function getMediaCollectionNameAttribute()
    {
        if ($value = Arr::get($this->media_questions, $this->type)) {
            $field_name = "{$value}_{$this->step->step_number}_{$this->name}";
        }

        return $field_name ?? $this->name;
    }

    public function getIsMediaQuestionAttribute()
    {
        return Arr::has($this->media_questions, $this->type);
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public function question_answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function question_answer_options()
    {
        return $this->hasMany(QuestionAnswerOption::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class)->orderBy('step_number');
    }

    public function getStepNumberAttribute()
    {
        return $this->step->step_number;
    }

    public function getLabel()
    {
        if ($this->label) {
            return $this->label;
        }

        return $this->placeholder;
    }

    public function getHasOptionsAttribute()
    {
        return $this->question_answer_options->count() > 0;
    }

    public function getOptionsArray()
    {
        if ($this->has_options) {
            return $this->getOptions()->toArray();
        }
        if ($this->options_callback) {
            return $this->getOptionsCallback();
        }

        return [];
    }

    public function getOptionsCallback()
    {
        $method = $this->options_callback;
        if (method_exists($this, $method)) {
            return $this->{$method};
        }

        return [];
    }

    public function getQuestionOptionsAttribute()
    {
        return $this->getOptionsArray();
    }

    public function getAllValidationRulesAttribute()
    {
        $all_rules = [];

        if ($this->required) {
            if ($this->is_dependend) {
                $depends_question = $this->depends_on_question;
                $depends_on_answer = $this->depends_on_answer;

                if ($depends_on_answer) {
                    $all_rules[] = 'required_if:' . $depends_question  . ',==,' . $depends_on_answer;
                } else {
                    $all_rules[] = 'required_with:' . $depends_question;
                }
            } else {
                $all_rules[] = ['required'];
            }
        }

        if ($this->validation_rules) {
            $all_rules[] = $this->validation_rules;
        }

        $digit_min = $this->digit_min;
        $digit_max = $this->digit_max;

        if ($this->type == 'range' || $this->type == 'number') {
            if ($digit_min) {
                $all_rules[] = "min:{$digit_min}";
            }

            if ($digit_max) {
                $all_rules[] = "max:{$digit_max}";
            }
        }

        $rules = array_flatten($all_rules);

        return $rules;
    }

    public function setDependsOnQuestionAttribute($value)
    {
        if (filled($value) && $value != 'none') {
            $this->is_dependend = true;
            $dep_question = self::where('name', $value)->first();
            if ($dep_question && !$dep_question->has_options) {
                $this->depends_on_answer = null;
            }
        } else {
            $this->is_dependend = false;
            $this->depends_on_answer = null;
        }

        return $this->depends_on_question = $value;
    }

    public function getAutocompleteAttribute($value)
    {
        if (!$value) {
            return 'off';
        }

        return $value;
    }

    public function getPrefillWithOptions()
    {
        return [
            'user.name' => __('Full Name'),
            'user.first_name' => __('First name'),
            'user.last_name' => __('Last name'),
            'user.email' => __('E-mail'),
            'addresses.address' => __('Street'),
            'addresses.house_number' => __('Housenumber'),
            'addresses.house_number_addon' => __('Housenumber addon'),
            'addresses.state' => __('Province'),
            'addresses.city' => __('City'),
            'addresses.postal_code' => __('Zipcode'),
            'addresses.vat_number' => __('VAT number'),
            'customer.company_name' => __('Company'),
            'customer.phone_number' => __('Phone'),
        ];
    }


    public function getAutocompleteOptions()
    {
        return [
            'organization' => __('Company'),
            'given-name' => __('First name'),
            'family-name' => __('Last name'),
            'email' => __('E-mail'),
            'tel' => __('Phone'),
            'address-line1' => __('Street'),
            'address-line2' => __('Housenumber'),
            'address-line3' => __('Housenumber addon'),
            'address-level1' => __('Province'),
            'address-level2' => __('City'),
            'postal-code' => __('Zipcode'),
            'country-name' => __('Country'),
        ];
    }

    /**
     * END TEST
     */


    public function getOptions()
    {
        $options = $this->question_answer_options->sortBy('order_column')->mapWithKeys(function ($option) {
            return [$option->key => $option->value];
        });
        return $options;
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('active', 1);
    }

    public function scopeOrdered(Builder $builder)
    {
        $builder->where('order', 'asc');
    }

    public function getInfo()
    {
        return strip_tags($this->info, ['a', 'br']);
    }

    public function getFieldValidationRulesAttribute()
    {
        $validation_rules = [
            'mimes:doc,docx,png,pdf' => __('Document'),
            'email:rfc,dns' => __('Email address'),
            'image' => __('Image'),
            'integer' => __('Integer'),
            'numeric' => ('Number'),
            'regex:/^([0-9\s\-\+\(\)]*)$/|min:10' => __('Phonenumber'),
            'url' => __('Website'),
            'regex:/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i' => __('Zipcode'),
        ];

        return $validation_rules;
    }

    public function getFieldTypesAttribute()
    {
        $question_types = [
            'media' => [
                'photo' => __('Image'),
                'photos' => __('Multiple images'),
            ],
            'input' => [
                'input' => __('Input'),
                'number' => __('Number'),
                'range' => __('Range'),
                'email' => __('Email'),
                'tel' => __('Phonenumber'),
                'color' => __('Color'),
                'url' => __('Website'),
            ],
            'text' => [
                'textarea' => __('Textarea'),
            ],
            'date' => [
                'date' => __('Date'),
                'datetime-local' => __('Datetime'),
                'month' => __('Month'),
                'time' => __('Time'),
                'week' => __('Week'),
            ],
            'choice' => [
                'radio' => __('Radio'),
                'checkbox' => __('Checkbox'),
                'select' => __('Select'),
            ],
        ];

        return $question_types;
    }

    public function getFieldTypesForSelectAttribute()
    {
        return array_collapse($this->field_types);
    }

    public function isInputField()
    {
        $fields = collect($this->field_types)->filter(function ($value, $key) {
            return $key == 'input' || $key == 'date';
        })->collapse()->toArray();
        return Arr::has($fields, $this->type);
    }
}

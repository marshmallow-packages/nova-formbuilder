<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Support\Str;
use Marshmallow\NovaFormbuilder\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Marshmallow\Nova\Flexible\Concerns\HasFlexible;

class Form extends Model
{
    // use Notifiable;
    // use HasExtraData;
    use SoftDeletes;
    use HasFlexible;
    use CascadeSoftDeletes;

    protected $cascadeDeletes = ['steps'];

    protected $guarded = [];

    protected $with = ['steps', 'questions'];

    protected $casts = [
        'layout' => FlexibleCast::class,
        'extra_data' => 'array',
    ];

    public function getFlexibleContentAttribute()
    {
        return $this->flexible('layout');
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active', 1);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->active()->orderBy('order');
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->active()->orderBy('step_number');
    }

    public function setAttribute($key, $value)
    {

        if (Str::startsWith($key, 'mm_extra_')) {
            return $this->storeExtraData($key, $value);
        }

        parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $attribute = parent::getAttribute($key);

        if ($attribute !== null) {
            return $attribute;
        }

        if (Str::startsWith($key, 'mm_extra_')) {
            return $this->getExtraData($key);
        }
    }

    public function templateable()
    {
        return $this->morphMany(Templateable::class, 'templateable');
    }
}

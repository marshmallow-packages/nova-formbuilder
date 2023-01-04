<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Marshmallow\NovaFormbuilder\Models\Question;
use Marshmallow\Nova\Flexible\Casts\FlexibleCast;
use Marshmallow\Nova\Flexible\Concerns\HasFlexible;
use Marshmallow\NovaFormbuilder\Models\Traits\HasExtraData;

class Form extends Model
{
    use SoftDeletes;
    use HasFlexible;
    use HasExtraData;
    use CascadeSoftDeletes;

    protected $table = 'nova_formbuilder_forms';

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
}

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

class Form extends Model
{
    use SoftDeletes;
    use HasFlexible;
    use CascadeSoftDeletes;

    protected $table = 'nova_formbuilder_forms';

    protected $cascadeDeletes = ['steps'];

    protected $guarded = [];

    protected $with = ['steps', 'questions'];

    protected $casts = [
        'layout' => FlexibleCast::class,
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
        return $this->hasMany(Question::class)->active()->ordered();
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->active()->orderBy('step_number');
    }
}

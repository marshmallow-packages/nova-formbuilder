<?php

namespace Marshmallow\NovaFormbuilder\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Step extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;
    use CascadeSoftDeletes;

    protected $table = 'nova_formbuilder_steps';

    protected $cascadeDeletes = ['questions'];

    protected $guarded = [];

    public $sortable = [
        'order_column_name' => 'step_number',
        'sort_when_creating' => false,
        'sort_on_has_many' => true,
    ];

    public function scopeActive(Builder $builder)
    {
        $builder->where('active', 1);
    }

    public function scopeOrdered(Builder $builder)
    {
        $builder->where('step_number', 'asc');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->active()->ordered();
    }
}

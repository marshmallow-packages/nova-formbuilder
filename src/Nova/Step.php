<?php

namespace Marshmallow\NovaFormbuilder\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Marshmallow\NovaSortable\Traits\HasSortableRows;

class Step extends Resource
{
    use HasSortableRows;

    public static $perPageViaRelationship = 15;

    public static $clickAction = 'detail';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Marshmallow\NovaFormbuilder\Models\Step::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title', 'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make(__('Form'), 'form', Form::class)
                ->withoutTrashed()
                ->help(__('The form that this step belongs to')),
            Text::make(__('Name'), 'name')->rules('required')->help(__('The name of this step (Only used for internal purposes)')),
            Textarea::make(__('Description'), 'description')->help(__('The description of this step (Displayed on the form)'))->hideFromIndex(),
            Text::make(__('Title'), 'title')->help(__('The title of this step (Displayed on the form)')),
            Text::make(__('Subtitle'), 'subtitle')->help(__('The subtitle of this step (Displayed on the form)'))->hideFromIndex(),
            Boolean::make(__('Active'), 'active')->default(true)->help(__('Is this step active?')),
            HasMany::make(__('Questions'), 'questions', Question::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}

<?php

namespace Marshmallow\NovaFormbuilder\Nova;


use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Marshmallow\Nova\TinyMCE\TinyMCE;
use Marshmallow\Nova\Flexible\Flexible;
use Laravel\Nova\Http\Requests\NovaRequest;

class Form extends Resource
{
    public static $clickAction = 'detail';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Marshmallow\NovaFormbuilder\Models\Form::class;

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
        'id', 'name'
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
            ID::make()->sortable(),
            Text::make(__('Name'), 'name')->rules('required')->sortable()->help(__('The name of the form')),
            Boolean::make(__('Active'), 'active')->help(__('Is the form active?')),
            HasMany::make(__('Steps'), 'steps', Step::class),
            // HasMany::make(__('Questions'), 'questions', Question::class),

            (new Panel(__('Form text'), [
                Flexible::make(__('Layout'), 'layout')->includeTags(['Form'])->fullwidth(false)
                    ->help(__('Layout that is used for the form'))->hideFromIndex(),
            ]))->collapsable(),

            (new Panel(__('Extra Fields'), $this->extraFields()))->collapsable(),
        ];
    }

    protected function extraFields()
    {
        return [
            Select::make(__('Width'), 'width')->options([
                '1/3' => __('1/3'),
                '1/4' => __('1/4'),
                '1/2' => __('1/2'),
                '2/3' => __('2/3'),
                '3/4' => __('3/4'),
                'full' => __('Full'),
            ])->displayUsingLabels()->default('full')->help(__('Width of the form')),

            Text::make(__('Cancel Redirect'), 'on_cancel')->help(__('Link if the \'Cancel\' button is pressed'))->readonly()->hideFromIndex(),
            Text::make(__('Submit Redirect'), 'on_submit')->help(__('Link if the \'Submit\' button is pressed'))->readonly()->hideFromIndex(),
            Text::make(__('Submit event class'), 'submit_event')->help(__('Event class that is, e.g. "\\App\\Events\\FormSub" '))->hideFromIndex(),
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

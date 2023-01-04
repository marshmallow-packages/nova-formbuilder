<?php

namespace Marshmallow\NovaFormbuilder\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Marshmallow\Nova\TinyMCE\TinyMCE;
use Laravel\Nova\Fields\BelongsToMany;
use Outl1ne\MultiselectField\Multiselect;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;


class FormSubmission extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Marshmallow\NovaFormbuilder\Models\FormSubmission::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title'
    ];

    public static function label()
    {
        return __('Form Leads');
    }

    public static function singularLabel()
    {
        return __('Form Lead');
    }

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
            BelongsTo::make(__('Form'), 'form', Form::class)->hideCreateRelationButton()->withoutTrashed()->readonly()->sortable(),
            MorphTo::make(__('Formable'), 'formable')->sortable(),
            Text::make(__('Title'), 'title')->readonly()->sortable(),
            TextArea::make(__('Description'), 'description')->readonly()->hideFromIndex(),
            Boolean::make(__('Submitted'), 'submitted')->readonly()->sortable(),
            DateTime::make(__('Submitted At'), 'submitted_at')->readonly()->sortable(),

            (new Panel('Media', $this->mediaFields()))->collapsable(),

            HasMany::make(__('Question Answers'), 'question_answers', QuestionAnswer::class)->readonly(),
        ];
    }

    protected function mediaFields()
    {
        return [

            Boolean::make(__('Has Images'), function ($model) {
                return $model->hasImages();
            })->readonly(),
            Images::make(__('Image'), 'form_image')->readonly()->hideFromIndex(),
            Images::make(__('Images'), 'form_images')->readonly()->hideFromIndex(),
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

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    // public function authorizedToDelete(Request $request)
    // {
    //     return false;
    // }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}

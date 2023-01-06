<?php

namespace Marshmallow\NovaFormbuilder\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Marshmallow\Nova\TinyMCE\TinyMCE;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasOneThrough;
use Marshmallow\Nova\Flexible\Flexible;
use Outl1ne\MultiselectField\Multiselect;
use Laravel\Nova\Http\Requests\NovaRequest;
use Marshmallow\NovaSortable\Traits\HasSortableRows;
use Marshmallow\NovaFormbuilder\Enums\QuestionFieldMap;
use Marshmallow\NovaFormbuilder\Nova\Layouts\QuestionOptionLayout;
use Marshmallow\NovaFormbuilder\Nova\Resolvers\QuestionOptionResolver;

class Question extends Resource
{
    use HasSortableRows;

    public static $perPageViaRelationship = 15;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Marshmallow\NovaFormbuilder\Models\Question::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'placeholder';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'placeholder'
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
                ->help(__('The form that this question belongs to')),

            BelongsTo::make(__('Step'), 'step', Step::class)
                ->withoutTrashed()
                ->help(__('The step that this question belongs to')),

            Boolean::make(__('Active'), 'active')->default(true)
                ->help(__('Is this question active?')),

            Text::make(__('Name'), 'name')->readonly()
                ->hideWhenCreating()
                ->help(__('The technical name of this question')),

            Text::make(__('Label'), 'label')->rules('required')
                ->help(__('The text above the field of this question')),

            Text::make(__('Placeholder'), 'placeholder')->rules('required')
                ->help(__('The text that is displayed in the field of this question')),

            Boolean::make(__('Required'), 'required')
                ->help(__('This questions needs to be filled in')),

            Select::make(__('Type'), 'type')
                ->options($this->resource->field_types_for_select)
                ->rules('required')->displayUsingLabels()
                ->help(__('The type of this field')),

            Select::make(__('Map to field'), 'field_map')->options(
                QuestionFieldMap::allOptionsAsArray()
            )->displayUsingLabels()->nullable()
                ->help(__('Map this field for the form to a user field')),

            Text::make(__('Prefix'), 'prefix')->hideFromIndex()->hide()->dependsOn(
                ['type'],
                function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type == 'input' || $formData->type == 'number') {
                        $field->show();
                    }
                }
            )->help(__('Show this at the start in the field of the question')),

            Text::make(__('Suffix'), 'suffix')->hideFromIndex()->hide()->dependsOn(
                ['type'],
                function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type == 'input' || $formData->type == 'number') {
                        $field->show();
                    }
                }
            )->help(__('Show this at the end in the field of the question')),

            Select::make(__('Width'), 'width')->options([
                '1/3' => __('1/3'),
                '1/4' => __('1/4'),
                '1/2' => __('1/2'),
                '2/3' => __('2/3'),
                '3/4' => __('3/4'),
                'full' => __('Full'),
            ])->displayUsingLabels()
                ->default('full')
                ->help(__('The width of this field on the website')),


            (new Panel(__('Help text'), [
                TinyMCE::make(__('Info'), 'info')->hideFromIndex()
                    ->help(__('Extra help text for the question')),

                Boolean::make(__('Show info as Tooltip'), 'info_tooltip')
                    ->default(false)
                    ->hideFromIndex()
                    ->help(__('Show Extra help text as tooltip next to the question')),
            ]))->collapsable(),

            Flexible::make(__('Question Options'), 'question_options_layout')
                ->addLayout(QuestionOptionLayout::class)
                ->resolver(QuestionOptionResolver::class)
                ->confirmRemove()
                ->collapsed()
                ->fullWidth(false)
                ->onlyOnForms()
                ->hide()
                ->help(__('The options of this question on the website (required if the question has options)'))
                ->dependsOn(
                    ['type'],
                    function (Flexible $field, NovaRequest $request, FormData $formData) {
                        if (
                            $formData->type == 'radio' || $formData->type == 'select' || $formData->type == 'checkbox'
                        ) {
                            $field->show()->rules('required');
                        }
                    }
                ),


            HasMany::make(__('Answer Options'), 'question_answer_options', QuestionAnswerOption::class),

            (new Panel(__('Extra Fields'), $this->extraFields()))->collapsable()
        ];
    }

    protected function extraFields()
    {
        return [
            Multiselect::make(__('Validation Rules'), 'validation_rules_set')
                ->hideFromIndex()
                ->options($this->resource->field_validation_rules)
                ->saveAsJSON()
                ->help(__('The validation rules of this question')),

            Multiselect::make(__('Autocomplete'), 'autocomplete')
                ->hideFromIndex()
                ->singleSelect()
                ->options($this->resource->getAutocompleteOptions())
                // ->saveAsJSON()
                ->nullable()
                ->help(__('The autocomplete rules for this question')),

            Multiselect::make(__('Prefill with'), 'prefill_with')
                ->hideFromIndex()
                ->singleSelect()
                ->options($this->resource->getPrefillWithOptions())
                // ->saveAsJSON()
                ->nullable()
                ->help(__('The prefill option for this question')),

            Number::make(__('Min number'), 'digit_min')
                ->hideFromIndex()->hide()->default(null)
                ->dependsOn('type', function ($field, $request, $formData) {
                    if ($formData->type == 'number' || $formData->type == 'range') {
                        $field->show()->rules('required');
                    } else {
                        $field->value = null;
                    }
                })->help(__('The minimum value of this question')),

            Number::make(__('Max number'), 'digit_max')
                ->hideFromIndex()->hide()
                ->dependsOn('type', function ($field, $request, $formData) {
                    if ($formData->type == 'number' || $formData->type == 'range') {
                        $field->show()->rules('required');
                    } else {
                        $field->value = null;
                    }
                })->help(__('The maximum value of this question')),

            Number::make(__('Steps'), 'digit_step')
                ->hideFromIndex()->hide()->default(null)
                ->dependsOn('type', function ($field, $request, $formData) {
                    if ($formData->type == 'range') {
                        $field->show()->rules('required');
                    }
                })->help(__('The steps value of this question')),

            Text::make(__('Validation Rules'), 'validation_rules')
                ->readonly()->hideFromIndex()
                ->help(__('The validation rules of this question')),

            Select::make(__('Depends on question'), 'depends_on_questions')
                ->hideFromIndex()->hide()->nullable()->dependsOn(
                    ['form'],
                    function (Select $field, NovaRequest $request, FormData $formData) {
                        if ($formData->form) {
                            $form = \Marshmallow\NovaFormbuilder\Models\Form::find($formData->form);
                            $questions = $form->questions->pluck('label', 'name')->toArray();
                            $questions['none'] = __('None');
                            $field->show()->options($questions);
                        }
                    }
                )->help(__('The question that this question depends on')),

            Select::make(__('Depends on answer'), 'depends_on_answer')->hideFromIndex()->nullable()->hide()->dependsOn(
                ['depends_on_questions'],
                function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->depends_on_questions) {
                        $question = \Marshmallow\NovaFormbuilder\Models\Question::whereName($formData->depends_on_questions)->first();
                        if ($question) {
                            if ($question->has_options) {

                                $options = $question->getOptionsArray();
                                $options['none'] = __('None');
                                $field->show()->options($options);
                            }
                        }
                    }
                }
            )->help(__('The answer of the question that this question depends on')),

            Text::make(__('Options Callback'), 'options_callback')
                ->readonly()->hideFromIndex()
                ->help(__('The callback that is used to get the options of this question')),

            // Text::make(__('Options'), 'options'),
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

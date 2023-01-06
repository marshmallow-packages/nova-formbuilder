<?php

// config for Marshmallow/NovaFormbuilder
return [
    'models' => [
        'form' => Marshmallow\NovaFormbuilder\Models\Form::class,
        'step' => Marshmallow\NovaFormbuilder\Models\Step::class,
        'question' => Marshmallow\NovaFormbuilder\Models\Question::class,
        'question_answer' => Marshmallow\NovaFormbuilder\Models\QuestionAnswer::class,
        'question_answer_option' => Marshmallow\NovaFormbuilder\Models\QuestionAnswerOption::class,
        'form_submission' => Marshmallow\NovaFormbuilder\Models\FormSubmission::class,
    ],

    // Get nova resources for this package
    'resources' => [
        'form' => Marshmallow\NovaFormbuilder\Nova\Form::class,
        'step' => Marshmallow\NovaFormbuilder\Nova\Step::class,
        'question' => Marshmallow\NovaFormbuilder\Nova\Question::class,
        'question_answer' => Marshmallow\NovaFormbuilder\Nova\QuestionAnswer::class,
        'question_answer_option' => Marshmallow\NovaFormbuilder\Nova\QuestionAnswerOption::class,
        'form_submission' => Marshmallow\NovaFormbuilder\Nova\FormSubmission::class,
    ],

    'resolver' => Marshmallow\NovaFormbuilder\Nova\Resolvers\QuestionOptionResolver::class,

    // This is the prefix for the session key per form
    'session_key_prefix' => 'mm_form_submission_',

    'spatie_media_library_pro' => false,
    'debug_forms' => env('APP_DEBUG', false),
];

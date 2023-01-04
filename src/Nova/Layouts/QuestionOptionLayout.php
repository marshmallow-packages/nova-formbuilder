<?php

namespace Marshmallow\NovaFormbuilder\Nova\Layouts;

use Laravel\Nova\Fields\Text;
use Marshmallow\Nova\Flexible\Layouts\Layout;

class QuestionOptionLayout extends Layout
{
    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'question_option';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Question Option';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make(__('Key'), 'key')->readonly(),
            Text::make(__('Value'), 'value'),
        ];
    }
}

<?php

namespace Marshmallow\NovaFormbuilder\Nova\Resolvers;

use Illuminate\Support\Arr;
use Marshmallow\Nova\Flexible\Value\ResolverInterface;

class QuestionOptionResolver implements ResolverInterface
{
    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Marshmallow\Nova\Flexible\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    {
        $options = $resource->question_answer_options()->orderBy('order_column')->get();

        $option_layouts = $options->map(function ($option) use ($layouts) {
            $layout = $layouts->find('question_option');

            if (!$layout) return;

            return $layout->duplicateAndHydrate($option->id, [
                'key' => $option->key,
                'value' => $option->value
            ]);
        })->filter();

        return $option_layouts;
    }

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return void
     */
    public function set($model, $attribute, $groups)
    {
        $class = get_class($model);

        $class::saved(function ($model) use ($groups) {
            $layout_options = $groups->map(function ($group, $index) use ($model) {
                $option_model_id = $group->inUseKey();
                $layout_key = $group->key();
                $attributes = $group->getAttributes();
                $index = $index + 1;
                $layout_option = [
                    'id' => $option_model_id,
                    'question_id' => $model->id,
                    'key' => $attributes['key'],
                    'value' => $attributes['value'],
                    'order_column' => $index
                ];
                if ($option_model_id == $layout_key) {
                    $layout_option['id'] = null;
                }
                return $layout_option;
            });

            // Get Base Option data
            $options = $model->question_answer_options()->get();

            // Get all Layout option Ids
            $layout_options_ids = $layout_options->pluck('id')->flatten();

            // Delete all options that are not in the layout
            $options->each(function ($option) use ($layout_options_ids) {
                if (!$layout_options_ids->contains($option->id)) {
                    $option->delete();
                }
            });

            // Update or Create all options that are in the layout
            $layout_options->each(function ($layout_option) use ($options, $model) {
                $layout_option_id = $layout_option['id'];
                $option = $options->firstWhere('id', $layout_option_id);
                if ($option) {
                    Arr::forget($layout_option, ['id']);
                    $option->update($layout_option);
                } else {
                    Arr::forget($layout_option, ['id', 'key']);
                    $option = $model->question_answer_options()->create($layout_option);
                }
                $option->save();
            });
        });
    }
}

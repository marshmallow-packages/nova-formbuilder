<?php

namespace Marshmallow\NovaFormbuilder\Http\Controllers;

use App\Http\Controllers\Controller;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Marshmallow\NovaFormbuilder\Models\Step;

class FormSubmissionController extends Controller
{
    /**
     * Validate and create a newly created Form Submission.
     *
     * @param  array  $input
     * @return \Marshmallow\NovaFormbuilder\Models\FormSubmission
     */
    public static function create($model, array $input, Step $step)
    {
        $dummy = false;

        if (empty($input)) {
            $data['model'] = [];
            $dummy = true;
        } else {
            $data = self::createFormSubmissionData($input);
        }

        $model = new FormSubmission();

        $morphClass = $model->getMorphClass();
        $form_id = $step->form_id;
        $create_data = [
            'form_id' => $form_id,
            'formable_id' => $model->id,
            'formable_type' => $morphClass,
            'submitted' => false,
        ];

        $form_submission = FormSubmission::updateOrCreate($create_data, $data['model']);

        if (! $dummy) {
            $form_submission->updateAnswerData($data['answers'], $step);
        }

        $session_key = config('nova-formbuilder.session_key_prefix').$form_id;
        $session_value = $form_submission->uuid;
        request()->session()->put($session_key, $session_value);

        return $form_submission;
    }

    public static function createFormSubmissionData($array)
    {
        $array = self::checkDataExistence($array, 'prefiller');
        $array = self::checkDataExistence($array, 'address_data');

        $data_array = [];

        $keys = [
            'zipcode',
            'house_number',
            'municipality_id',
            'province_id',
            'city_id',
            'country_id',
        ];

        $model_array = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $model_array[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        $data_array['model'] = $model_array;
        $data_array['answers'] = $array;

        return $data_array;
    }

    public static function checkDataExistence($array, $key)
    {
        $new_array = [];
        if (array_key_exists($key, $array)) {
            $new_array = $array[$key];
            $new_array = self::checkFieldNames($new_array, 'housenumber', 'house_number');
            unset($array[$key]);
        }
        $array = array_merge($new_array, $array);

        return $array;
    }

    public static function checkFieldNames($array, $old_name, $new_name)
    {
        if (array_key_exists($old_name, $array)) {
            $array[$new_name] = $array[$old_name];
            unset($array[$old_name]);
        }

        return $array;
    }
}

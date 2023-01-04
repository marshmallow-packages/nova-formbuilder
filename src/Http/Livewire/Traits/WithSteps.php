<?php

namespace Marshmallow\NovaFormbuilder\Http\Livewire\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

trait WithSteps
{
    use UsesSpamProtection;

    public HoneypotData $extraFields;

    public $isMainComponent = false;
    public $state = [];
    public $hasMedia = false;

    public $view = 'forms.create';
    public $media_questions;

    public $my_rules = [];

    public $formComponent = 'MMForms:Form';
    public $stepComponent = 'MMForms:Step';
    public $questionComponent = 'MMForms:Question';


    /**
     * Livewire lifecycle methods
     */
    public function hydrateWithSteps()
    {
        $stepComponent = 'MMForms:Step';
        $listeners = [
            'nextStep',
            'previousStep',
            'setModelId',
        ];

        $stepComponent = $this->stepComponent;
        foreach ($listeners as $listener) {
            $this->listeners["$stepComponent:$listener"] = $listener;
        }
    }

    public function mountWithSteps()
    {
        $this->mergeRules($this->questions);
        $this->extraFields = new HoneypotData();
    }

    /**
     * Set SubmissionData
     */
    public function setFormSubmissionData($data)
    {
        $this->form_submission = $data;
        $this->model_id = $data['id'];
        $this->form_submission_model = FormSubmission::find($this->model_id);
    }

    /**
     * Simple Get & Set Functions
     */
    public function setModelId($model_id)
    {
        $this->model_id = $model_id;
    }

    public function nextStep(): void
    {
        // ray('withSteps - nextStep')->purple();
    }

    public function previousStep(): void
    {
        // ray('withSteps - previousStep')->purple();
    }

    public function submitStep(): void
    {
        ray("Submit Step {$this->stepNumber} - Submit Data", $this->state)->blue();

        $this->resetErrorBag();

        $this->protectAgainstSpam();

        foreach ($this->state as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $value_key => $value_value) {
                    if (!$value_value) {
                        unset($this->state[$key][$value_key]);
                    }
                }
            }
            if (in_array($key, $this->mediaComponentNames)) {
                $this->saveMedia($key, $value);
            }
        }

        Validator::make($this->state, $this->my_rules)->validate();
        $this->emit('stepSaved');

        $this->emit("{$this->formComponent}:submitData", $this->stepNumber, $this->state);
    }

    /**
     * Question Rule Functions
     */
    public function mergeRules($questions)
    {
        $questions->each(function ($question) {
            if (!$question->active) {
                return;
            }
            $rules = [];

            $name = $question->media_collection_name;
            $step = $question->step_number;
            $rules = $question->all_validation_rules;

            if ($this->stepNumber == $step) {
                $this->my_rules[$name] = $rules;
            }
        });
    }

    /**
     * Delete Media
     */
    public function deleteMedia($key, $value, $model)
    {
        $answers = $model->getMediaAnswers();
        if (empty($answers)) {
            return;
        }

        if ($value == $this->$key) {
            return;
        }
        if (is_string($value)) {
            $values[] = $value;
        } elseif (is_array($value)) {
            $values = $value;
        } else {
            $values = null;
        }

        if (is_array($values) && is_array($this->$key)) {

            $media_uuids = [];
            foreach ($this->$key as $photo) {
                $media_uuids[] = $photo['uuid'];
            }
            $media_uuids = array_diff_assoc($values, $media_uuids);
            // $media_uuids = array_diff($values, $media_uuids);

            $medias = Media::whereIn('uuid', $media_uuids)->get();
            if ($medias) {
                $medias->each(function ($media) {
                    $media->delete();
                });
            }
        } else {
            if ($values) {
                $medias = Media::whereIn('uuid', $values)->get();
                if ($medias) {
                    $medias->each(function ($media) {
                        $media->delete();
                    });
                }
            }
        }

        $this->state[$key] = null;
    }


    /**
     * Add Media
     */
    public function saveMedia($key, $value)
    {
        $model = $this->model::find($this->model_id);
        $media_question = $this->questions->where('media_collection_name', $key)->first();

        if (empty($this->$key) || (is_array($value) && count($this->$key) !== count($value))) {
            $this->deleteMedia($key, $value, $model);
        }

        if ($this->hasMedia && $media_question && $this->$key && !empty($this->$key)) {

            $multiple = false;
            if ($media_question->type == 'photos') {
                $multiple = true;
            }

            if ($multiple && $value !== $this->$key) {
                $collection_name = $key;
                $model->syncFromMediaLibraryRequest($this->$key)
                    ->toMediaCollection($collection_name);

                $media_uuids = [];
                foreach ($this->$key as $photo) {
                    $media_uuids[] = $photo['uuid'];
                }

                $this->state[$key] = $media_uuids;
            } elseif (!$multiple && $value !== $this->$key) {
                $collection_name = $key;
                $model->addFromMediaLibraryRequest($this->$key)
                    ->toMediaCollection($collection_name);

                $media_uuid = collect($this->$key)->first()['uuid'];

                $this->state[$key] = $media_uuid;
            }

            $this->$key = null;

            // $this->clearMedia($key);
        }
    }
}

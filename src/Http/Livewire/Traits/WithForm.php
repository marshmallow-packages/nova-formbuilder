<?php

namespace Marshmallow\NovaFormbuilder\Http\Livewire\Traits;

use Marshmallow\NovaFormbuilder\Models\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Marshmallow\NovaFormbuilder\Events\FormSubmissionEvent;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Illuminate\Support\Facades\Auth;
use Marshmallow\NovaFormbuilder\Http\Livewire\Traits\WithSteps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Marshmallow\NovaFormbuilder\Http\Controllers\FormSubmissionController;

trait WithForm
{
    public $form_id;
    public $session_key;
    public $session_value;

    public $currentStep = 1;
    public $firstStep = 1;
    public $lastStep = 1;

    public $submitted = false;
    public $full_width = false;

    public $form;
    public $questions;
    public $steps;

    public $formSteps;
    public $formStepsArray;

    public $form_submission_id;
    public $form_submission_data;

    public $submission_morph_model_class;
    public $submission_morph_model_id;

    public $formComponent = 'MMForms:Form';
    public $stepComponent = 'MMForms:Step';
    public $questionComponent = 'MMForms:Question';

    protected $end_page_layout_name = 'form-end-page';
    public $end_page_layout = [
        'thanks_title' => null,
        'thanks_text' => null,
    ];

    /**
     * Livewire lifecycle methods
     */
    public function hydrateWithForm()
    {
        $listeners = [
            'submitMain',
            'submitData',
            'previousStep',
            'nextStep'
        ];
        $formComponent = $this->formComponent;
        foreach ($listeners as $listener) {
            $this->listeners["$formComponent:$listener"] = $listener;
        }

        $this->queryString['submitted'] = ['except' => false];
    }

    public function mountWithForm()
    {
        $form = Form::findOrFail($this->form_id);

        if ($form->width == 'full') {
            $this->full_width = true;
        }

        $this->session_key = "form_submission_{$this->form_id}";
        if (session()->has($this->session_key)) {
            $this->session_value = session($this->session_key);
            $form_submission = FormSubmission::where('uuid', $this->session_value)->first();
            if ($form_submission && $form_submission->id) {
                $this->form_submission_id = $form_submission->id;
            }
        }

        $end_page_layout = $form->layout->find($this->end_page_layout_name);
        $this->end_page_layout['thanks_title'] = $end_page_layout?->thanks_title;
        $this->end_page_layout['thanks_text'] = $end_page_layout?->thanks_text;

        $this->form = $form->toArray();
        $this->questions = $form->questions;
        $this->formSteps = $form->steps;

        $this->firstStep = $this->formSteps->min('step_number');
        $this->lastStep = $this->formSteps->max('step_number');

        $this->currentStep = request()->has('step') ? request()->step : $this->firstStep;

        $this->formStepsArray = $this->formSteps->mapWithKeys(function ($step) {
            return [$step['step_number'] => $step];
        })->toArray();

        if ($this->form_submission_id) {
            $this->getFormSubmissionData();
        } else {
            $this->createFormSubmission([]);
        }

        if ($this->submitted) {
            $this->currentStep = $this->lastStep;
        }

        $this->steps = $this->questions->mapToGroups(function ($item, $key) {
            $stepNumber = $item->step_number;
            return [$stepNumber => $item];
        })->map(function ($collection) {
            return $collection->sortBy(function ($item) {
                return $item->order;
            })->values();
        })->sortKeys();
    }


    /**
     * Form Main Step Functions
     */
    public function submitMain()
    {
        ray('Main Submit - Complete')->green();

        $on_submit = $this->form['on_submit'];

        if (session()->has($this->session_key)) {
            session()->forget($this->session_key);
        }

        $this->submitted = true;
        if (!Str::startsWith($on_submit, '?')) {
            redirect()->to($on_submit);
        }
    }

    public function getCurrentStepModel($steps = null)
    {
        if ($steps) {
            return $steps->where('step_number', $this->currentStep)->first();
        } else {
            return $this->formSteps->where('step_number', $this->currentStep)->first();
        }
    }

    /**
     * Go to next step
     */
    public function nextStep(): void
    {
        if ($this->currentStep < $this->lastStep) {
            $this->currentStep = $this->currentStep + 1;
            $this->emit("{$this->stepComponent}:nextStep");
        }
    }

    /**
     * Go to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep <= $this->lastStep) {
            $this->currentStep = $this->currentStep - 1;
            $this->emit("{$this->stepComponent}:previousStep");
        }
    }

    /**
     * Form Submission Functions
     */
    public function getSubmissionModelType()
    {
        $model = Auth::user();

        $model_class = $this->submission_morph_model_class;
        $model_id = $this->submission_morph_model_id;
        if ($model_class && $model_id) {
            $model = $model_class::find($model_id);
        }

        return $model;
    }

    /**
     * Submit data to the form submission.
     */
    public function submitData(int $stepNumber, array $data): void
    {
        if ($stepNumber == 0) {
            $this->nextStep();
            return;
        }

        ray("Submit On Main: Step {$stepNumber} - Complete", $data)->orange();

        if ($stepNumber == $this->lastStep) {
            if (!$this->form_submission_id) {
                $form_submission = $this->createFormSubmission($data);
                $this->form_submission_id = $form_submission->id;
            }
            $this->updateFormSubmission($data, true);
            $this->submitMain();
        } elseif ($stepNumber == $this->firstStep) {
            $form_submission = $this->createFormSubmission($data);
            $this->form_submission_id = $form_submission->id;
        } else {
            $this->updateFormSubmission($data);
            ray('Main Submit - Step Data', $data);
        }

        $this->nextStep();

        $this->getFormSubmissionData();
    }

    /**
     * Get the form_submission.
     *
     * @param array $input
     * @return FormSubmission
     */
    public function getFormSubmissionData()
    {
        $form_submission = FormSubmission::find($this->form_submission_id);
        $this->form_submission_data = $form_submission->toArray();
        $this->emit("$this->stepComponent:setFormSubmissionData", $this->form_submission_data);
    }

    /**
     * Create the  form_submission.
     *
     * @param array $input
     * @return FormSubmission
     */
    public function createFormSubmission(array $input)
    {
        $step = $this->getCurrentStepModel();

        if ($this->form_submission_id) {
            return $this->updateFormSubmission($input);
        }

        $model = $this->getSubmissionModelType();
        return FormSubmissionController::create($model, $input, $step);
    }

    /**
     * Update the form_submission.
     *
     * @param array $data
     * @return void
     */
    public function updateFormSubmission(array $input, $last = false): FormSubmission
    {
        $step = $this->getCurrentStepModel();
        $form_submission = FormSubmission::find($this->form_submission_id);
        unset($input['prefiller']);

        $form_submission->updateAnswers($input, $step);

        if ($last) {
            $form_submission->finalize();
        }

        return $form_submission;
    }
}

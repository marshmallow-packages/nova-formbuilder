<?php

namespace Marshmallow\NovaFormbuilder\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Marshmallow\NovaFormbuilder\Http\Livewire\Traits\WithSteps;
use Marshmallow\NovaFormbuilder\Models\Form;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Spatie\MediaLibraryPro\Http\Livewire\Concerns\WithMedia;

class Step extends Component
{
    use WithSteps;
    use WithMedia;

    public $form_id;

    public $stepNumber;

    public $form_submission;

    public $full_width = false;

    public $sidebar = false;

    public FormSubmission $form_submission_model;

    protected $listeners = [
        'setFormSubmissionData',
        'setStateData',
        'setFormState',
    ];

    public $model = \Marshmallow\NovaFormbuilder\Models\FormSubmission::class;

    public $model_id;

    public $media_collection = 'form_images';

    public $media_collection_single = 'form_image';

    public $session_key;

    public $session_value;

    public $mediaComponentNames = [];

    public $has_autocomplete = false;

    public function setFormState($type, $value)
    {
        $this->$type = $value;
    }

    public function setStateData($data)
    {
        $this->state = array_merge($this->state, $data);
    }

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount($step, $questions, $form_id, $form_submission_data = [])
    {
        $this->stepNumber = $step;

        $form = Form::findOrFail($form_id);
        if ($form->width == 'full') {
            $this->full_width = true;
        }

        $this->session_key = config('nova-formbuilder.session_key_prefix').$form_id;
        if (! empty($form_submission_data)) {
            $this->setFormSubmissionData($form_submission_data);
        } elseif (session()->has($this->session_key)) {
            $this->session_value = session($this->session_key);
            $data = FormSubmission::where('uuid', $this->session_value)->first()->toArray();
            $this->setFormSubmissionData($data);
        }

        if (is_array($questions)) {
            $this->questions = $form->questions->where('step', $this->stepNumber)->sortBy('order_column');
        } else {
            $this->questions = $questions;
        }

        if ($this->form_submission) {
            $form_submission_id = $this->form_submission['id'];
            $form_submission = FormSubmission::find($form_submission_id);

            $answers = $form_submission->getAnswers();

            $this->questions->each(function ($question) use ($answers) {
                $name = $question->name;

                if ($question->field_map && $this->user) {
                    $field_name = $question->field_map->value;
                    $value = $this->user->$field_name;
                    if ($value) {
                        $value = $this->user->$field_name;
                        $this->state[$name] = $value;
                    }
                }

                if (array_key_exists($name, $answers)) {
                    $this->state[$name] = $answers[$name];
                }
            });
        }

        $photos = $this->questions->filter(function ($question) {
            if ($question->type == 'photos' || $question->type == 'photo') {
                $question->state_name = 'state.'.$question->name;
                if ($question->type == 'photo') {
                    $question->multiple_photos = false;
                } else {
                    $question->multiple_photos = true;
                }
                $this->mediaComponentNames[] = $question->media_collection_name;

                return $question;
            }
        });

        if (count($photos) > 0) {
            $this->hasMedia = true;
            $this->mediaComponentNames = collect($this->mediaComponentNames)->unique()->toArray();
            foreach ($this->mediaComponentNames  as $mediaComponent) {
                $this->listeners["$mediaComponent:mediaChanged"] = 'onMediaChanged';
                $this->state[$mediaComponent] = [];
            }
            $this->media_questions = $photos;
        }

        $this->questions->each(
            function ($question) {
                if ($question->autocomplete !== 'off') {
                    $this->has_autocomplete = true;
                }
                if ($question->prefill_with) {
                    if (array_has($this->state, $question->name) && $this->state[$question->name] && ! empty($this->state[$question->name])) {
                        return;
                    }
                    [$type, $field] = explode('.', $question->prefill_with);

                    if ($this->$type && $this->$type->$field) {
                        $answer = $this->$type->$field;
                    }
                    if (isset($answer) && filled($answer)) {
                        $this->state[$question->name] = $answer;
                    }
                }
            }
        );
    }

    public function render()
    {
        $this->showDepends();

        return view('nova-formbuilder::livewire.step');
    }

    public function showDepends()
    {
        $dependents = [];
        $this->questions = $this->questions->map(function ($question) use (&$dependents) {
            $depended = $question->is_dependend;
            $question->dependend = $depended ? true : false;
            $question->display = $depended ? false : true;
            if ($depended) {
                $state_has_answer = false;
                $question->depends_question = $question->depends_on_question;
                $dependents[] = $question->depends_question;
                $question->depends_answer = $question->depends_on_answer;
                $state_has_question = Arr::has($this->state, $question->depends_question);
                if ($state_has_question) {
                    $state_answer = $this->state[$question->depends_question];
                    if ($state_answer && filled($state_answer)) {
                        if ($question->depends_answer && filled($question->depends_answer)) {
                            if (is_array($state_answer) && filled($state_answer)) {
                                $state_has_answer = in_array($question->depends_answer, $state_answer);
                            } else {
                                $state_has_answer = $state_answer == $question->depends_answer;
                            }
                        } else {
                            if (is_array($state_answer) && count($state_answer) == 1) {
                                $answer = Arr::first($state_answer);
                                if ($answer) {
                                    $state_has_answer = true;
                                }
                            } else {
                                $state_has_answer = true;
                            }
                        }
                    }
                }
                $question->display = $state_has_answer;
            }

            return $question;
        });

        $this->questions = $this->questions->map(
            function ($question) use ($dependents) {
                $question->has_dependents = false;
                if (in_array($question->name, $dependents)) {
                    $question->has_dependents = true;
                }

                return $question;
            }
        );
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user() ?? null;
    }
}

<?php

namespace Marshmallow\NovaFormbuilder\Events;

use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class FormSubmissionMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $form_submission;
    public $answers;

    public function __construct(FormSubmission $form_submission)
    {
        $this->form_submission = $form_submission;
        $this->answers = $form_submission->answers;
    }
}

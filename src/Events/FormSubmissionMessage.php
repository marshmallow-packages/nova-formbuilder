<?php

namespace Marshmallow\NovaFormbuilder\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Marshmallow\NovaFormbuilder\Models\FormSubmission;

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

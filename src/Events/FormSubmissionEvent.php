<?php

namespace Marshmallow\NovaFormbuilder\Events;

use Marshmallow\NovaFormbuilder\Models\FormSubmission;
use Marshmallow\NovaFormbuilder\Events\FormSubmissionMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class FormSubmissionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FormSubmission $form_submission)
    {
        $submit_event = $form_submission->form->submit_event;

        if (class_exists($submit_event)) {
            $event = new $submit_event($form_submission);
            return event($event);
        }

        return event(new FormSubmissionMessage($form_submission));
    }
}

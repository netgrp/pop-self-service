<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Agent\Agent;

class EmailNotFound extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $location;
    public $agent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$location, Agent $agent)
    {
        $this->user = $user;
        $this->location = $location;
        $this->agent = $agent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.email-not-found')
                    ->subject('Nulstil kodeord til '.config('app.name'));
    }
}

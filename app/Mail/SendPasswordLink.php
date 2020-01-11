<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Agent\Agent;

class SendPasswordLink extends Mailable
{
    use Queueable;
    use SerializesModels;
    protected $agent;
    public $user;
    public $platform;
    public $browser;
    public $pass;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, Agent $agent, $pass)
    {
        $this->user = $user;
        $this->agent = $agent;

        if ($this->agent->platform() != '') {
            $this->platform = $this->agent->platform();
        } else {
            $this->platform = 'Ukendt';
        }

        if ($this->agent->browser() != '') {
            $this->browser = $this->agent->browser();
        } else {
            $this->browser = 'Ukendt';
        }

        $this->pass = $pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.send-password-link')
                    ->subject('Nulstil kodeord til '.config('app.name'));
    }
}

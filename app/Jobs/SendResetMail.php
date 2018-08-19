<?php

namespace App\Jobs;

use App\Knet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class SendResetMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $location;
    protected $agent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$ipaddress,Agent $agent)
    {
        $this->email = $email;
        $this->location = Location::get($ipaddress);
        $this->agent = $agent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Initilize K-net API
        $knet = new Knet();

        // Search for user
        $this->user = $knet->findByEmail($this->email);

        // Send e-mail, either reset link, or info about user not found.
        if ($this->user == null) {
            // User not found
            Mail::to([['email' => $this->email]])->send(new \App\Mail\EmailNotFound);
        }
        else
        {
            Mail::to([['name' => $this->user['name'],'email' => $this->email]])->send(new \App\Mail\SendPasswordLink);

        }

        // Log the attempt to reset in the database with ip, useragent, and $user
    }
}

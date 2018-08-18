<?php

namespace App\Jobs;

use App\Knet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Agent\Agent;

class SendResetMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$ipaddress,Agent $agent)
    {
        $this->email = $email;
        $this->ipaddress = $ipaddress;
        $this->location = Location::get($ipaddress);
        $this->headers = $headers;
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

        }
        else
        {
            // User found

        }

        // Log the attempt to reset in the database with ip, useragent, and $user
    }
}

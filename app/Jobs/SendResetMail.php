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

class SendResetMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $ipaddress;
    protected $agent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$ipaddress,Agent $agent)
    {
        $ipaddress = '82.211.217.109';
        $this->email = $email;
        $this->ipaddress = $ipaddress;
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
        $user = $knet->findByEmail($this->email);

        // Save request to database, and get reset token.
        

        // Send e-mail, either reset link, or info about user not found.
        if ($user == null) {
            // User not found
            Mail::to($user = [['email' => $this->email]])->send(new \App\Mail\EmailNotFound($user[0],$this->agent));
        }
        else
        {
            //Send e-mail
            Mail::to($user = [['name' => $user['name'],'email' => $this->email]])->send(new \App\Mail\SendPasswordLink($user[0],$this->agent));
        }

        // Log the attempt to reset in the database with ip, useragent, and $user
    }
}

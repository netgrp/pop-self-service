<?php

namespace App\Jobs;

use App\Knet;
use App\ResetRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class SendResetMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $ipaddress;
    protected $agent;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$ipaddress,Agent $agent,$userAgent)
    {
        $ipaddress = '82.211.217.109';
        $this->email = $email;
        $this->ipaddress = $ipaddress;
        $this->agent = $agent;
        $this->userAgent = $userAgent;
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
        $resetRequest = new ResetRequests();
        $resetRequest->pass = Str::random(40);
        $resetRequest->email = $this->email;
        $resetRequest->userAgent = $this->userAgent;
        $resetRequest->ipaddress = $this->ipaddress;
        $resetRequest->user = json_encode($user);
        $resetRequest->save();

        // Send e-mail, either reset link, or info about user not found.
        if ($user == null) {
            // User not found
            Mail::to($user = [['email' => $this->email]])->send(new \App\Mail\EmailNotFound($user[0],$this->agent));
        }
        else
        {
            //Send e-mail
            Mail::to($user = [['name' => $user['name'],'email' => $this->email]])->send(new \App\Mail\SendPasswordLink($user[0],$this->agent,route('reset.show', ['pass' => $resetRequest])));
        }

        // Log the attempt to reset in the database with ip, useragent, and $user
    }
}

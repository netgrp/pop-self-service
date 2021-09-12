<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetMail;
use App\Knet;
use App\ResetRequests;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ResetRequestsController extends Controller
{
    public function __construct()
    {
        $agent = new Agent();
        if ($agent->browser() == 'IE') {
            echo 'Internet Explorer is not supported while in beta. Please use any other browser.';
            exit();
        }
        if ($agent->isRobot()) {
            echo 'No robots allowed here.';
            exit();
        }
    }

    public function index()
    {
        return view('index');
    }

    public function show(ResetRequests $pass)
    {
        // VIGTIGT TILFØJ 24 timers begrænsing, UDLØB!
        if (!$pass->valid) {
            return view('invalid');
        }

        // Show it by the id
        $user = json_decode($pass->user);
        $userinfo = [];

        return view('show', compact('userinfo', 'pass'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'               => 'required|email',
            'consent'             => 'required|boolean',
            'g-recaptcha-response'=> 'required|captcha',
        ]);

        // Bedre løsning på dettte!
        if (!$validated['consent']) {
            return 'no consent';
        }

        $agent = new Agent();

        SendResetMail::dispatch($validated['email'], $request->ip(), $agent, $request->header('user-agent'));

        return [
            'roomok' => true,
            'sendok' => true,
        ];
    }

    public function patch(ResetRequests $pass, Request $request)
    {
        // VIGTIGT TILFØJ 24 timers begrænsing, UDLØB!
        if (!$pass->valid) {
            return [
                'sendok' => false,
            ];
        }

        $validated = $request->validate([
            'username_reset' => 'nullable|in:normalize,email',
            'password'       => 'required|string|min:6|pwned|confirmed',
        ]);

        // Get the user
        $user = json_decode($pass->user);

        // Initilize K-net API
        $knet = new Knet();

        // Initilize username reset
        $username_reset = '';

        // Ignore username_reset. If $username_reset is '', then nothing is changed.

        // Patch user
        $result = $knet->patchUser($user->url, $validated['password'], $username_reset);

        // Mark requested as used, to prevent duplicate changes with same token
        $pass->completed = true;
        $pass->save();

        return [
            'sendok' => $result,
        ];
    }
}

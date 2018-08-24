<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetMail;
use App\ResetRequests;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ResetRequestsController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function show(ResetRequests $pass)
    {
        // VIGTIGT TILFØJ 24 timers begrænsing, UDLØB!

        // Show it by the id
        $user = json_decode($pass->user);
        $user->username = "Emil - 073";
        $userinfo = [];

        // Check if username matches e-mail
        if ($pass->email != $user->username) {
            $userinfo['email'] = $pass->email;
        }

        // Skal brugernavnstjek tillade e-mail, eller skal dette tjek blot fjernes hvis brugernavn er e-mail? Så kan regex ændres til /^[a-z0-9]*$/
        if (!preg_match('/^[a-z0-9@.]*$/', $user->username)) {
            $userinfo['normalized'] = preg_replace('/[^a-z0-9@.]/', '',strtolower($user->username));
        }

        // Hvis normalized brugernavn er e-mail, så skal den ik stå dobbelt
        if (isset($userinfo['normalized']) && isset($userinfo['email']) && $userinfo['normalized'] == $userinfo['email']) {
            unset($userinfo['email']);
        }

        // Hvis nyt brugernavn forslås, skal orignal inkluderes.
        if ($userinfo != []) {
            $userinfo['unchanged'] = $user->username;
        }

        return view('show', compact('userinfo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'email' => 'required|email',
        'consent' => 'required|boolean',
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

    public function patch(Request $request)
    {
        $validated = $request->validat([
            'username_reset' => 'required|nullable|in:normalize,email',
            'password' => 'required|confirmed',
        ]);
    }
}

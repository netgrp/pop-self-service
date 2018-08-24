<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetMail;
use App\Knet;
use App\ResetRequests;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ResetRequestsController extends Controller
{
    public function __construct() {
        $agent = new Agent();
        if ($agent->browser() == "IE") {
            echo "Internet Explorer is not supported while in beta. Please use Chrome, Firefox, Safari og Edge.";
            exit();
        }
        if ($agent->isRobot()) {
            echo "No robots allowed here.";
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
        if (!$pass->valid)
        {
            return view('invalid');
        }

        // Show it by the id
        $user = json_decode($pass->user);
        $userinfo = [];

        // Check if username matches e-mail
        if ($pass->email != $user->username) {
            $userinfo['email'] = $pass->email;
        }

        // Skal brugernavnstjek tillade e-mail, eller skal dette tjek blot fjernes hvis brugernavn er e-mail? Så kan regex ændres til /^[a-z0-9]*$/
        // BEMÆRK: DENNE ÆNDRING ER HARDCODED MERE END EN GANG I KODEN, UNDEN FUNKTION!
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

        return view('show', compact('userinfo','pass'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'consent' => 'required|boolean',
            'g-recaptcha-response'=>'required|captcha',
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
        if (!$pass->valid)
        {
            return [
                'sendok' => false,
            ];
        }

        $validated = $request->validate([
            'username_reset' => 'nullable|in:normalize,email',
            'password' => 'required|confirmed',
        ]);

        // Get the user
        $user = json_decode($pass->user);

        // Initilize K-net API
        $knet = new Knet();

        // Initilize username reset
        $username_reset = '';

        // Set username to e-mail if requested
        if ($validated['username_reset'] == 'email')
        {
            $username_reset = $user->email;
        }

        // Normalize username if requested
        if ($validated['username_reset'] == 'normalize')
        {
            $username_reset = preg_replace('/[^a-z0-9@.]/', '',strtolower($user->username));
        }

        // Patch user
        $result = $knet->patchUser($user->url,$validated['password'],$username_reset);

        // Mark requested as used, to prevent duplicate changes with same token
        $pass->completed = true;
        $pass->save();

        return [
            'sendok' => $result,
        ];
    }
}

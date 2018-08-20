<?php

namespace App\Http\Controllers;

use App\ResetRequests;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ResetRequestsController extends Controller
{
    public function index() {
    	return view('index');
    }

    public function show(ResetRequests $pass) {
    	// Show it by the id
    	$user = json_decode($pass->user);

    	// Check if username matches e-mail
    	if ($pass->email != $user->username) {
    		// Do this if username isn't the email
    		echo "Username isn't email";
    	}

    	// Skal brugernavnstjek tillade e-mail, eller skal dette tjek blot fjernes hvis brugernavn er e-mail? Så kan regex ændres til /^[a-z0-9]*$/
    	if (!preg_match('/^[a-z0-9@.]*$/', $user->username)) {
    		// Do this if the username contains anything else than a-z0-9
    		echo "Username format is bad";
    	}

    	// return view?
    }
}
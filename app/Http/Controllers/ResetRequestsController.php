<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ResetRequestsController extends Controller
{
    public function index() {
    	return view('index');
    }

    public function show() {
    	// Show it by the id
    }
}

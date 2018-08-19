<?php

use App\Jobs\SendResetMail;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/resetPassword', function (Request $request) {
	$validated = $request->validate([
		'email' => 'required|email',
	]);

	$agent = new Agent();

	SendResetMail::dispatch($validated['email'],$request->ip(),$agent,$request->header('user-agent'));

    return [
    	'roomok' => true,
    	'sendok' => true,
    ];
});
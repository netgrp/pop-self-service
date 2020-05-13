<?php

namespace App\Http\Controllers;

use App\Knet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HeartbeatController extends Controller
{
    // Basic idea is to check intern services first, from most likely to fail to least likely. That way we avoid checking services more often than necessary.
    public function index()
    {
        // Check database connection to primary database
        $this->checkDatabase('', 'primary');

        // K-Net API
        $knet = new Knet();
        $user = $knet->findByEmail('this-email-can-never-exist@pop.dk');
        if ($user != null) {
            Log::error('Heartbeat: Unexpected response from K-net API.');
            abort(503); // Service Unavailable
        }

        // Mailgun API
        // Disabled mailgun api check due to false positivies
        // $this->checkMailgun();

        // Check recaptcha api
        // https://www.google.com/recaptcha/api/siteverify
        $this->checkGoogleRecaptha();
    }

    public function checkDatabase($db_name, $description)
    {
        try {
            DB::connection($db_name)->getPdo();
            if (!DB::connection()->getDatabaseName()) {
                Log::error('Heartbeat: Could not find the '.$description.' database. Please check your configuration.');
                abort(503); // Service Unavailable
            }
        } catch (\Exception $e) {
            Log::error('Heartbeat: Could not open connection to the '.$description.' database server.  Please check your configuration. Exception: '.$e);
            abort(503); // Service Unavailable
        }
    }

    public function checkMailgun()
    {
        $ch = curl_init();
        $curlConfig = [
            CURLOPT_URL            => 'https://api.mailgun.net/v4/address/validate',
            CURLOPT_TIMEOUT        => 1,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => 'api:'.env('MAILGUN_SECRET'),
        ];
        curl_setopt_array($ch, $curlConfig);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // Expect 400 bad request since no e-mail was given
        if ($httpcode != 400) {
            // Tip: http code 401 means the API key is invalid
            Log::error('Heartbeat: Unexpected response from Mailgun API');
            abort(503); // Service Unavailable
        }
    }

    public function checkGoogleRecaptha()
    {
        $ch = curl_init();
        $curlConfig = [
            CURLOPT_URL            => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_TIMEOUT        => 1,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $curlConfig);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // Expect 200, that the page loads
        if ($httpcode != 200) {
            // Tip: http code 401 means the API key is invalid
            Log::error('Heartbeat: Unexpected response from Mailgun API');
            abort(503); // Service Unavailable
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Knet extends Model
{
	protected $ch;
	protected $headers;
    protected $apikey;

    public function __construct($apikey = '') {
    	// Check if libcurl is enabled
    	if (!function_exists('curl_init')) { die("ERROR: Please enable php-curl\n"); }

    	// Public cURL handle (we want to reuse connections)
    	$this->ch = curl_init();
    	$this->headers = [
    		'authorization' => 'Authorization: Basic ' . base64_encode($apikey),
        ];
        $this->apikey = $apikey;
    }

    protected function httpHeaders($o=[]) {
    	$ret = $this->headers;
    	if (isset($o['headers'])) {
            foreach($o['headers'] as $key => &$val) {
                $ret[strtolower($key)] = $key . ': ' . $val;
            }
        }
        return array_values($ret);
    }

    protected function request($path, $opts=[], $data=null) {
    	$hostname = (isset($opts['hostname'])) ? $opts['hostname'] : 'k-net.dk';
    	$curlopts = [
            CURLOPT_URL => 'https://' . $hostname . $path,
            CURLOPT_HTTPHEADER => $this->httpHeaders($opts),
            CURLOPT_CUSTOMREQUEST => ($data === null) ? 'GET' : 'PATCH',
            CURLOPT_POSTFIELDS => ($data === null) ? null : $data,
            CURLOPT_VERBOSE => isset($opts['debug']) ? $opts['debug'] : 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USE_SSL => CURLUSESSL_ALL,
            CURLOPT_SSLVERSION => 6, // TLSv1.2
        ];
        // Allow override of $curlopts.
        if (isset($opts['curl'])) {
            foreach($opts['curl'] as $key => &$val) { $curlopts[$key] = $val; }
        }
        curl_setopt_array($this->ch, $curlopts);

        $result = curl_exec($this->ch);
        if (!$result) {
            throw new \Exception(curl_strerror(curl_errno($this->ch)));
        }

        $statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            throw new \Exception(explode("\n", $result)[0]);
        }
        // Decode the json response (@: surpress warnings)
        if (!$resobj = @json_decode($result, true)) {
            throw new \Exception('Invalid response from server');
        }
        return $resobj;
    }
}

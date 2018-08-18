<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Knet extends Model
{
    protected $ch;
    protected $headers;
    protected $apikey;

    public function __construct($apikey = '')
    {
        // Check if libcurl is enabled
        if (!function_exists('curl_init')) {
            die("ERROR: Please enable php-curl\n");
        }

        // API key from .env or directly
        $this->apikey = ($apikey == '') ? env('KNET_API_KEY') : $apikey;

        // Public cURL handle (we want to reuse connections)
        $this->ch = curl_init();
        $this->headers = [
            'authorization' => 'Authorization: Basic ' . base64_encode($this->apikey),
        ];
    }

    protected function httpHeaders($o=[])
    {
        $ret = $this->headers;
        if (isset($o['headers'])) {
            foreach ($o['headers'] as $key => &$val) {
                $ret[strtolower($key)] = $key . ': ' . $val;
            }
        }
        return array_values($ret);
    }

    protected function request($path, $opts=[], $data=null)
    {
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
            foreach ($opts['curl'] as $key => &$val) {
                $curlopts[$key] = $val;
            }
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

    protected function random_str(
    $length = 12,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public function getAllUsers()
    {
        return $this->request('/api/v2/network/user/?page_size=0');
    }

    public function findByEmail($email)
    {
        $email = strtolower($email);
        $users = $this->getAllUsers();

        $matches = [];

        foreach ($users as $key => $value) {
            if (strtolower($value['username']) == $email || strtolower($value['email']) == $email) {
                $matches[] = $key;
            }
        }

        if (count($matches) > 1) {
            throw new \Exception('More than one entry found. Must be unique');
        }

        if (isset($matches[0])) {
            return $users[$matches[0]];
        } else {
            return null;
        }
    }

    public function passwordSetter($url, $password)
    {
        // Check url format, exception if wrong

    	// Generate salt
    	$salt = $this->random_str();

        //calculate new hashes
        $data = [
            'password' => 'sha1$'.$salt.'$'.hash('sha1',$salt.$password,false),
            'password_nt' => hash('md4', iconv('UTF-8', 'UTF-16LE', $password), false),
        ];

        return $data;

        //Patch those

        //confirm
    }
}

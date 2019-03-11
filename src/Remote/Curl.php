<?php

namespace Jawn\Remote;

class Curl
{
    /**
     * GET
     * @param string URI
     * @param mixed content for POST and PUT methods
     * @param array headers ex. ['x-access-token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9']
     * @param array curl options
     * @return array of 'raw', 'headers', 'content', 'error'
     */
    public function get(
        string $uri,
        $data = null,
        array $curl_headers = [],
        array $curl_options = []
    ): array {
        return self::send($uri, 'GET', $data, $curl_headers, $curl_options);
    }

    /**
     * POST
     * @param string URI
     * @param mixed content for POST and PUT methods
     * @param array headers ex. ['x-access-token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9']
     * @param array curl options
     * @return array of 'raw', 'headers', 'content', 'error'
     */
    public function post(
        string $uri,
        $data = null,
        array $curl_headers = [],
        array $curl_options = []
    ): array {
        return self::send($uri, 'POST', $data, $curl_headers, $curl_options);
    }

    /**
     * PUT
     * @param string URI
     * @param mixed content for POST and PUT methods
     * @param array headers ex. ['x-access-token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9']
     * @param array curl options
     * @return array of 'raw', 'headers', 'content', 'error'
     */
    public function put(
        string $uri,
        $data = null,
        array $curl_headers = [],
        array $curl_options = []
    ): array {
        return self::send($uri, 'PUT', $data, $curl_headers, $curl_options);
    }

    /**
     * DELETE
     * @param string URI
     * @param mixed content for POST and PUT methods
     * @param array headers ex. ['x-access-token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9']
     * @param array curl options
     * @return array of 'raw', 'headers', 'content', 'error'
     */
    public function delete(
        string $uri,
        $data = null,
        array $curl_headers = [],
        array $curl_options = []
    ): array {
        return self::send($uri, 'DELETE', $data, $curl_headers, $curl_options);
    }

    /**
     * Wrapper for easy cURLing
     * @param string URI
     * @param string HTTP method (GET|POST|PUT|DELETE)
     * @param mixed content for POST and PUT methods
     * @param array headers ex. ['x-access-token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9']
     * @param array curl options
     * @return array of 'raw', 'headers', 'content', 'error'
     */
    private function send(
        string $uri,
        string $method = 'GET',
        $data = null,
        array $curl_headers = [],
        array $curl_options = []
    ): array {
        // defaults
        $default_curl_options = [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
        ];
        $default_headers = [];

        // validate input
        $method = strtoupper(trim($method));
        $allowed_methods = [
            'GET',
            'POST',
            'PUT',
            'DELETE'
        ];

        if (!in_array($method, $allowed_methods)) {
            throw new \Exception("'$method' is not valid cURL HTTP method.");
        }

        if (!empty($data) && !is_string($data)) {
            throw new \Exception("Invalid data for cURL request '$method $uri'");
        }

        // init
        $curl = curl_init($uri);

        // apply default options
        curl_setopt_array($curl, $default_curl_options);

        // apply method specific options
        if ($method === 'GET') {
            // nothing -> not even sure why I left this...
        } elseif ($method === 'POST') {
            if(!is_string($data)) {
                throw new \Exception("Invalid data for cURL request '$method $uri'");
            }
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } elseif ($method === 'PUT') {
            if(!is_string($data)) {
                throw new \Exception("Invalid data for cURL request '$method $uri'");
            }
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } elseif ($method === 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // apply user options
        curl_setopt_array($curl, $curl_options);

        // add headers
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array_merge($default_headers, $curl_headers)
        );

        // parse result
        $raw = rtrim(curl_exec($curl));
        $lines = explode("\r\n", $raw);
        $headers = [];
        $content = '';
        $write_content = false;
        if (count($lines) > 3) {
            foreach ($lines as $h) {
                if ($h == '') {
                    $write_content = true;
                } else {
                    if ($write_content) {
                        $content .= $h."\n";
                    } else {
                        $headers[] = $h;
                    }
                }
            }
        }
        $error = curl_error($curl);

        curl_close($curl);

        return [
            'raw' => $raw,
            'headers' => $headers,
            'content' => $content,
            'error' => $error,
        ];
    }
}

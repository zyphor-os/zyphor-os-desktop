<?php

namespace App\Smark;

/**
 * get($url, $accessToken = '', $verification = true)
 * - Makes a GET request to the given URL
 * - Optionally includes access token in Authorization header
 * - Optionally disables SSL verification
 * - Detects if response is JSON or plain text
 *
 * post($url, $body = [], $accessToken = '', $verification = true)
 * - Makes a POST request to the given URL with body
 * - Automatically sends JSON-encoded body
 * - Optionally includes access token
 * - Optionally disables SSL verification
 * - Detects if response is JSON or plain text
 */

class Api
{
    // Makes a GET request to the given URL
    public static function get($url, $accessToken = '', $verification = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verification);

        $headers = [];
        if (!empty($accessToken)) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => $error];
        }

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if (stripos($contentType, 'application/json') !== false) {
            return json_decode($response, true);
        }

        return $response;
    }

    // Makes a POST request to the given URL with JSON body
    public static function post($url, $body = [], $accessToken = '', $verification = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verification);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $headers = ['Content-Type: application/json'];
        if (!empty($accessToken)) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => $error];
        }

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if (stripos($contentType, 'application/json') !== false) {
            return json_decode($response, true);
        }

        return $response;
    }
}

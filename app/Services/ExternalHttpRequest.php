<?php

namespace App\Services;

class ExternalHttpRequest
{
    /**
     * This makes a post request call to an external service
     * Example of the headers :
     * ["Content-Type: application/json", "Authorization: Bearer bearer_code_here"]
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return mixed
     * @throws \JsonException
     */
    public static function processPostRequest(string $uri, array $data = [], array $headers = [])
    {
//        Log::info('header', $headers);
        $payload = json_encode($data, JSON_THROW_ON_ERROR);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    /**
     * This makes a get request call to an external service
     * @param string $uri
     * Example of the headers :
     * ["Content-Type: application/json", "Authorization: Bearer bearer_code_here"]
     * @param array $headers
     * @return mixed
     * @throws \JsonException
     */
    public static function processGetRequest(string $uri, array $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    /**
     * This makes a post request call to an external service
     * Example of the headers :
     * ["Content-Type: application/json", "Authorization: Bearer bearer_code_here"]
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return mixed
     * @throws \JsonException
     */
    public static function processPutRequest(string $uri, array $data = [], array $headers = [])
    {
//        Log::info('header', $headers);
        $payload = json_encode($data, JSON_THROW_ON_ERROR);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

}

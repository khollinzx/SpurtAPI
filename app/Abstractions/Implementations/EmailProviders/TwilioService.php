<?php

namespace App\Abstractions\Implementations\SMSProviders;

use App\Abstractions\AbstractClasses\SMSClass;
use App\Abstractions\Interfaces\SMSProviderInterface;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService extends SMSClass implements SMSProviderInterface
{
    /**
     * @return string
     */
    public function sid(): string
    {
        return env('TWILIO_SID');
    }

    /**
     * @return string
     */
    public function secretToken(): string
    {
        return env('TWILIO_TOKEN');
    }

    /**
     * @return string
     */
    public function API(): string
    {
        return env('TWILIO_API');
    }

    /**
     * @param array $payload
     * @return string|Client
     */
    public function initialize(array $payload = [])
    {
//        try {
//
//           return new Client(self::sid(), self::secretToken());
//
//        } catch (ConfigurationException $e) {
//            Log::info($e);
//
//            return  $e->getMessage();
//        }
        try {
            $post = http_build_query($payload);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "{$this->API()}{$this->sid()}/Messages.json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_USERPWD => "{$this->sid()}:{$this->secretToken()}",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/x-www-form-urlencoded',
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);
            Log::info($response. $post);
            curl_close($curl);

        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    /**
     * @param string $recipient
     * @param bool $isOTP
     * @param int|null $code
     * @return bool
     */
    public function sendSMS(string $recipient, bool $isOTP = true, int $code = null): bool
    {
//        $client = (new self())->initialize();
        $phone = '+'.$recipient;
        $body = $isOTP?
            "Your OTP is $code"
            : "Kindly click http://onelink.to/9hfqdv to download the Treepz Mobile App";

        $payload = [
            "From" => $this->sender(),
            "To"   => $phone,
            "Body" => $body,
        ];

        $this->initialize($payload);//send SMS

        return true;
//        try {
//
//            $response = $client->messages->create($phone, ['from' => $this->sender(), 'body' => $body ]);
//            Log::info($response.self::sid());
//            return true;
//
//        } catch (TwilioException $e) {
//            Log::error($e->getMessage().self::sid());
//
//            return false;
//        }
    }

    /**
     * @param string $recipient
     * @param string $body
     * @return bool
     */
    public function sendSMSNotification(string $recipient, string $body): bool
    {
        $phone = '+'.$recipient;

        $payload = [
            "From" => $this->sender(),
            "To"   => $phone,
            "Body" => $body,
        ];

        $this->initialize($payload);//send SMS

        return true;
//        $client = (new self())->initialize();
//        $phone = '+' . $recipient;
//
//        try {
//
//            $response = $client->messages->create($phone, ['from' => $this->sender(), 'body' => $body ]);
//            Log::info($response);
//
//            return true;
//
//        } catch (TwilioException $e) {
//            Log::error($e->getMessage().self::sid());
//            return false;
//        }
    }
}

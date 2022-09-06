<?php

namespace App\Abstractions\Implementations\EmailProviders;

use App\Abstractions\AbstractClasses\EmailClass;
use App\Abstractions\Interfaces\EmailProviderInterface;
use Illuminate\Support\Facades\Log;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

class MailgunService extends EmailClass implements EmailProviderInterface
{

    /**
    * @return string
    */
    public function getKey(): string
    {
        return env('MAIL_GUN_API_KEY');
    }

    /**
     * @param array $data
     */
    public function initialize(array $data = [])
    {
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/{$this->sender()}/messages");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_USERPWD, 'api' . ':' . $this->getKey());

            $result = curl_exec($ch);
//            Log::info('Message: '.$result);
            if (curl_errno($ch))
                Log::error('Error:' . curl_error($ch));

            curl_close($ch);

        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    /**
     * Add this to Queue in the future
     * @param array $emailConfig
     * @param string $bladeTemplate
     * @param array $bladeData
     * @param string $subject
     * @return bool
     */
    public function sendMail(array $emailConfig, string $bladeTemplate, array $bladeData = [], string $subject = ''): bool
    {
        $postData = [
            'from'    =>  $emailConfig['sender_name'] . " <" . $emailConfig['sender_email'] . ">",
            'to'      =>  $emailConfig['recipient_name'] . " <" . $emailConfig['recipient_email'] . ">",
            'subject' =>  $emailConfig['subject']?? $subject,
            'html'    =>  $this->loadTemplateView($bladeTemplate, $bladeData)//fetch the content of the blade template
        ];

        $this->initialize($postData);//sends the mail

        return true;
    }
}

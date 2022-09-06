<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

class EmailHelper
{
    /**
     * @param Model $model
     * @param array $data
     * @param string $template
     * @throws TypeException
     */
    public function SendMail(Model $model, array $data, string $template = 'emails.otp_mail')
    {

        $email = new Mail();
        $email->setFrom("collinsbenson0039@gmail.com", "Collins");
        $email->setSubject("Account Activation");
        $email->addTo($model->email, $model->first_name);
        $view = View::make($template, $data);
        $html = $view->render();//fetch the content of the blade template
        $email->addContent( "text/html", $html);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            Log::info([$response]);
//            print $response->statusCode() . "\n";
//            print_r($response->headers());
//            print $response->body() . "\n";

        } catch (TypeException $e) {
            Log::error($e->getMessage());
//            echo 'Caught exception: '. $e->getMessage() ."\n";
//            return false;
        }
    }

    public function SendConsultantPassword(Model $model, array $data, string $template = 'emails.otp_mail')
    {

        $email = new Mail();
        $email->setFrom("collinsbenson0039@gmail.com", "Collins");
        $email->setSubject("Account Activation");
        $email->addTo($model->email, $model->name);
        $view = View::make($template, $data);
        $html = $view->render();//fetch the content of the blade template
        $email->addContent( "text/html", $html);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            Log::info([$response]);
//            print $response->statusCode() . "\n";
//            print_r($response->headers());
//            print $response->body() . "\n";

        } catch (TypeException $e) {
            Log::error($e->getMessage());
//            echo 'Caught exception: '. $e->getMessage() ."\n";
//            return false;
        }
    }
}

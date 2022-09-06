<?php

namespace App\Services;

use App\Models\EmailProvider;
use App\Models\PaymentProvider;
use Illuminate\Support\Str;

class PaymentService
{

    /**
     * @var string|null
     */
    var $PAYMENT_SERVICE = null;

    private function setPaymentProvider(string $className): void
    {
        #Get the active service provider from Database
//        $active_provider = (new PaymentProvider())->getActiveProvider();
//        if($active_provider)
//            $this->PAYMENT_SERVICE = Str::of(trim($active_provider->class))->studly();

            $this->PAYMENT_SERVICE = Str::of(trim($className?? "PaystackService"))->studly();

        $service = "\App\Abstractions\Implementations\PaymentProviders\\" . $this->PAYMENT_SERVICE;
        if(class_exists($service)) {
            $this->PAYMENT_SERVICE = new $service();
//            Log::info("Sending email through: $service");
        }
    }

    /**
     * This gets the current active Mail service provider
     * @return mixed
     */
    public function getProvider(string $className)
    {
        self::setPaymentProvider($className);
        return new $this->PAYMENT_SERVICE();
    }
}

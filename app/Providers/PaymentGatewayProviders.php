<?php

namespace App\Providers;

use App\Abstractions\Interfaces\PaymentGatewayInterface;
use App\Http\Controllers\InvoiceController;
use App\Models\PaymentProvider;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class PaymentGatewayProviders extends ServiceProvider
{
    protected $paymentProvider;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
//        $this->bindEmailServiceProviderByClassName();
        $request = $this->app->request;

        if($request->has('payment_gateway_class_name'))
            $this->bindPaymentGatewayServiceProviderByClassName($request->payment_gateway_class_name);

        else //else return a default service
            $this->bindPaymentGatewayServiceProviderByClassName('PaystackNigeriaService');
    }

    /**
     * This binds a Mail Service Provider through the Defined Class
     * @return mixed|void
     */
    private function bindPaymentGatewayServiceProviderByClassName(string $className)
    {
        $this->app->when(InvoiceController::class)
        ->needs(PaymentGatewayInterface::class)
        ->give(function () use ($className)
        {
            $service = "\App\Abstractions\Implementations\PaymentProviders\\" . $className;

            if(class_exists($service))
                return new $service();
        });
//        $this->app->when(PaymentService::class)
//            ->needs(PaymentGatewayInterface::class)
//            ->give(function ()
//            {
//                #Get the active service provider from Database
//                $active_provider = (new PaymentProvider())->getActiveProvider();
//                if($active_provider)
//                {
//                    $class = Str::of(trim($active_provider->class?? 'StripeService'))->studly();
//                    $service = "\App\Abstractions\Implementations\PaymentProviders\\" . $class;
//
//                    if(class_exists($service))
//                    {
//                        Config::set('payment.provider', $service);
//
//                        return new $service();
//                    }
//                }
//            });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(PaymentProvider $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

}

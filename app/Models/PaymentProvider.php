<?php

namespace App\Models;

use App\SMSProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentProvider extends Model
{
    use HasFactory;

    //List of supported providers
    const PROVIDERS = [
        'Stripe'
    ];

    /**
     * @var string
     */
    protected $table = 'payment_providers';

    protected $fillable = [
        'title',
        'enabled',
        'class'
    ];

    /**
     * This deactivates all SMS providers
     */
    private function deactivateAllProvider()
    {
        DB::statement("UPDATE `payment_providers` SET `enabled` = 0;");
    }

    /**
     * checks if a Provider already exists
     * @param string $title
     * @return mixed
     */
    private static function checkIfAvailable(string $title)
    {
        return self::where('title', ucwords($title))->first();
    }

    /**
     * @param string $title
     */
    public static function add(string $title)
    {
        $provider = new self();
        $provider->title = ucwords($title);
        $provider->enabled = $title === 'Stripe'? 1 : 0;
        $provider->class = ucwords(str_replace(' ', '', $title)) . "Service";
        $provider->save();
    }

    /**
     * Creates a new Provider
     */
    public static function createAllProviders()
    {
        if(count(self::PROVIDERS))
            foreach (self::PROVIDERS as $provider)
                if(!self::checkIfAvailable($provider))
                    self::add($provider);

    }

    /**
     * This should be called to enable a particular Provider
     * @param PaymentProvider $provider
     */
    public function activateProvider(self $provider)
    {
        $this->deactivateAllProvider();

        $provider->enabled = 1;
        $provider->save();
    }

    /**
     * This gets the active Provider
     */
    public function getActiveProvider()
    {
        return self::where('enabled', 1)->first();
    }
}

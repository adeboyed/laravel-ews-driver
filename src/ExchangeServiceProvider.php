<?php

namespace Adeboyed\LaravelExchangeDriver;

use Adeboyed\LaravelExchangeDriver\ExchangeAddedTransportManager;
use Illuminate\Mail\MailServiceProvider;

class ExchangeServiceProvider extends MailServiceProvider
{

    public function register()
    {
        parent::register();

        $this->app->register(ExchangeAddedTransportManager::class);
    }
    
}

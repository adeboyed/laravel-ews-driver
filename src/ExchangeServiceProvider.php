<?php

namespace Adeboyed\LaravelExchangeDriver;

use Adeboyed\LaravelExchangeDriver\ExchangeAddedServiceProvider;
use Illuminate\Mail\MailServiceProvider;

class ExchangeServiceProvider extends MailServiceProvider
{

    public function register()
    {
        parent::register();

        $this->app->register(ExchangeAddedServiceProvider::class);
    }
}

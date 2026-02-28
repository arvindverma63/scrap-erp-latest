<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interface\AuditRepositoryInterface;
use App\Repositories\AuditRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Interface\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    public function boot(): void
    {
        //
    }
}

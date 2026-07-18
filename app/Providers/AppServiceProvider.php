<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Payments\PaymentService::class,
            \App\Services\Payments\MidtransService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production only
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Event auto-discovery is enabled, manual bindings removed to prevent duplicate execution

        // Global Event Traceability
        \Illuminate\Support\Facades\Event::listen('App\Events\*', function (string $eventName, array $data) {
            $event = $data[0] ?? null;
            if ($event && property_exists($event, 'order')) {
                \App\Models\OrderEventLog::create([
                    'order_id' => $event->order->id ?? null,
                    'event_name' => class_basename($eventName),
                    'stage' => $event->order->processing_stage ?? null,
                    'payload' => json_encode($event)
                ]);
            }
        });

        // Register Brevo custom transport
        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config = []) {
            return new \App\Mail\Transports\BrevoTransport(
                $config['key'] ?? env('BREVO_API_KEY')
            );
        });
    }
}

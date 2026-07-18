<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductionDiagnosticsCommand extends Command
{
    protected $signature = 'app:diagnostics';
    protected $description = 'Run production diagnostics for Resend and Biteship';

    public function handle()
    {
        $this->info("==================================================");
        $this->info("PRIORITAS 1 — VERIFIKASI RAILWAY PRODUCTION");
        $this->info("==================================================");

        $this->checkEnv('MAIL_MAILER');
        $this->checkEnv('RESEND_API_KEY');
        $this->checkEnv('MAIL_FROM_ADDRESS');
        $this->checkEnv('MAIL_FROM_NAME');
        $this->checkEnv('BITESHIP_API_KEY');
        $this->checkEnv('BITESHIP_ORIGIN_AREA_ID');

        $this->info("");
        $this->info("==================================================");
        $this->info("PRIORITAS 2 — MIGRATION PRODUCTION");
        $this->info("==================================================");

        $hasBiteshipAreaId = Schema::hasColumn('addresses', 'biteship_area_id');
        $this->line("Schema::hasColumn('addresses', 'biteship_area_id') => " . ($hasBiteshipAreaId ? 'true' : 'false'));

        $this->info("");
        $this->info("==================================================");
        $this->info("MAIL PRODUCTION DIAGNOSTIC (HTTPS API)");
        $this->info("==================================================");

        $this->line("config('mail.default') => " . config('mail.default'));
        
        $this->checkEnv('MAIL_MAILER');
        
        $apiKey = env('BREVO_API_KEY');
        $this->line('BREVO_API_KEY: ' . ($apiKey ? 'PRESENT' : 'MISSING'));
        
        $this->checkEnv('MAIL_FROM_ADDRESS');
        $this->checkEnv('MAIL_FROM_NAME');

        $this->line("Config cached: " . (app()->configurationIsCached() ? 'YES' : 'NO'));

        $this->info("");
        $this->info("==================================================");
        $this->info("PRIORITAS 4 — TEST EMAIL NYATA (BREVO API)");
        $this->info("==================================================");

        try {
            // Attempt to send email to public recipient
            Mail::raw('Umar Bakery API production email test.', function ($message) {
                $message->to('umarramadhan10@gmail.com')
                        ->subject('Umar Bakery Production API Test');
            });
            $this->info("API_ACCEPTED: Email request accepted by provider to umarramadhan10@gmail.com without exceptions. (Delivery Unconfirmed)");
        } catch (Throwable $e) {
            $this->error("Email request FAILED!");
            $this->line("Exception: " . get_class($e));
            $this->line("Message: " . $e->getMessage());
        }

        $this->info("");
        $this->info("==================================================");
        $this->info("PRIORITAS 6 — BITESHIP PRODUCTION");
        $this->info("==================================================");

        $biteKey = config('services.biteship.api_key');
        $biteOrigin = config('services.biteship.origin_area_id');

        $this->line("BITESHIP_API_KEY: " . ($biteKey ? 'PRESENT' : 'NULL'));
        $this->line("BITESHIP_ORIGIN_AREA_ID: " . ($biteOrigin ?: 'NULL'));

        if ($biteKey) {
            $this->info("\nTesting Maps API for Origin:");
            try {
                $resp = Http::withToken($biteKey)->get('https://api.biteship.com/v1/maps/areas', [
                    'countries' => 'ID',
                    'input' => 'Purwantoro, Wonogiri, Jawa Tengah',
                    'type' => 'single',
                ]);
                $this->line("HTTP Status: " . $resp->status());
                $areas = $resp->json('areas', []);
                if (!empty($areas)) {
                    $this->line("Found ID: " . $areas[0]['id']);
                    $this->line("Found Name: " . $areas[0]['name']);
                } else {
                    $this->error("No areas found.");
                }
            } catch (Throwable $e) {
                $this->error("Maps API Origin FAILED: " . $e->getMessage());
            }

            $this->info("\nTesting Maps API for Destination (Tapos, Kota Depok, Jawa Barat):");
            $destId = null;
            try {
                $resp = Http::withToken($biteKey)->get('https://api.biteship.com/v1/maps/areas', [
                    'countries' => 'ID',
                    'input' => 'Tapos, Kota Depok, Jawa Barat',
                    'type' => 'single',
                ]);
                $this->line("HTTP Status: " . $resp->status());
                $areas = $resp->json('areas', []);
                if (!empty($areas)) {
                    $destId = $areas[0]['id'];
                    $this->line("Found Destination ID: " . $destId);
                    $this->line("Found Name: " . $areas[0]['name']);
                } else {
                    $this->error("No areas found.");
                }
            } catch (Throwable $e) {
                $this->error("Maps API Destination FAILED: " . $e->getMessage());
            }

            if ($destId && $biteOrigin) {
                $this->info("\nTesting Rates API:");
                try {
                    $resp = Http::withToken($biteKey)->post('https://api.biteship.com/v1/rates/couriers', [
                        'origin_area_id' => $biteOrigin,
                        'destination_area_id' => $destId,
                        'items' => [
                            [
                                'name' => 'Test Item',
                                'description' => 'Test',
                                'value' => 20000,
                                'length' => 30,
                                'width' => 20,
                                'height' => 15,
                                'weight' => 500,
                            ]
                        ]
                    ]);
                    $this->line("HTTP Status: " . $resp->status());
                    if ($resp->successful()) {
                        $pricings = $resp->json('pricing', []);
                        $this->line("Couriers found: " . count($pricings));
                        foreach (array_slice($pricings, 0, 3) as $p) {
                            $this->line(" - " . $p['courier_name'] . " (" . $p['courier_service_code'] . "): Rp " . $p['price']);
                        }
                    } else {
                        $this->error("Rates API FAILED: " . $resp->body());
                    }
                } catch (Throwable $e) {
                    $this->error("Rates API FAILED: " . $e->getMessage());
                }
            }
        }
        
        $this->info("\nDone.");
    }

    private function checkEnv($key)
    {
        $val = env($key);
        if ($val === null) {
            $this->line("$key: NULL");
        } else {
            $this->line("$key: PRESENT");
        }
    }
}

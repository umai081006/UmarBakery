<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class ProductionDiagnostics extends Command
{
    protected $signature = 'orders:diagnostics';

    protected $description = 'Run production diagnostics for payment expiry system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- PRODUCTION DIAGNOSTICS ---');
        
        // 1. Timezone & Time Check
        $appTime = now();
        $dbTime = \Illuminate\Support\Facades\DB::selectOne('SELECT CURRENT_TIMESTAMP as time')->time;
        
        $this->info('Application Timezone: ' . config('app.timezone'));
        $this->info('Application Time   : ' . $appTime->format('Y-m-d H:i:s'));
        $this->info('Database Time      : ' . $dbTime);
        
        // 2. Latest Pending Payment
        $latestPending = \App\Models\Payment::where('status', 'pending')
                            ->whereNotNull('expires_at')
                            ->latest()
                            ->first();
                            
        if ($latestPending) {
            $this->info("\n--- LATEST PENDING PAYMENT ---");
            $this->info('Payment ID  : ' . $latestPending->id);
            $this->info('Order ID    : ' . $latestPending->order_id);
            $this->info('Created At  : ' . $latestPending->created_at->format('Y-m-d H:i:s'));
            $this->info('Expires At  : ' . $latestPending->expires_at->format('Y-m-d H:i:s'));
            
            $remaining = $appTime->diffInSeconds($latestPending->expires_at, false);
            if ($remaining > 0) {
                $this->info("Status      : ACTIVE ($remaining seconds remaining)");
            } else {
                $this->warn("Status      : EXPIRED (" . abs($remaining) . " seconds ago)");
            }
        } else {
            $this->info("\nNo pending payments found with an expiry time.");
        }
        
        // 3. Scheduler registration info
        $this->info("\n--- SCHEDULER INFO ---");
        $this->info("To run the expiry command automatically in production (Railway):");
        $this->info("1. Create a new service from the same repo.");
        $this->info("2. Set the start command to: php artisan schedule:work");
        $this->info("Alternatively, use a Railway Cron Job running: php artisan schedule:run");
        
        $this->info("\nDiagnostics Complete.");
    }
}

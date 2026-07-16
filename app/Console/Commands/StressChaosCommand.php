<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Artisan;
use App\Events\PaymentPaid;
use App\Events\PaymentFailed;
use App\Models\Payment;
use Illuminate\Support\Facades\Event;

class StressChaosCommand extends Command
{
    protected $signature = 'stress:chaos';
    protected $description = 'Run a realistic stress test simulating production chaos.';

    public function handle()
    {
        $this->info("--- STARTING CHAOS STRESS TEST ---");

        // 1. Prepare Environment
        $this->info("\n[1] Preparing Environment...");
        Artisan::call('migrate:fresh', ['--seed' => true]);
        
        $category = \App\Models\Category::firstOrCreate(['slug' => 'chaos-category'], ['name' => 'Chaos Category']);
        $product = Product::create([
            'sku' => 'CHAOS-01',
            'name' => 'Limited Chaos Item',
            'slug' => 'limited-chaos-item',
            'description' => 'Only 5 in stock!',
            'price' => 100000,
            'stock' => 5, // Exact limit to trigger overselling vulnerabilities
            'weight' => 1000,
            'is_active' => true,
            'category_id' => $category->id
        ]);

        $users = [];
        for ($i = 1; $i <= 30; $i++) {
            $users[] = User::create([
                'name' => "Chaos User $i",
                'email' => "chaos_$i@example.com",
                'password' => bcrypt('password'),
                'phone' => "08000000$i"
            ]);
        }
        $this->info("Created 30 users and 1 product with 5 stock.");

        // 2. Multi-User Checkout Race
        $this->info("\n[2] Initiating Concurrent Checkout Race (30 Processes)...");
        $processes = [];
        $pipes = [];
        
        foreach ($users as $user) {
            $cmd = "php artisan stress:checkout-worker {$user->id} {$product->id}";
            $descriptorSpec = [
                0 => ["pipe", "r"],  // stdin
                1 => ["pipe", "w"],  // stdout
                2 => ["pipe", "w"]   // stderr
            ];
            
            $process = proc_open($cmd, $descriptorSpec, $pipesForProcess);
            if (is_resource($process)) {
                $processes[] = [
                    'process' => $process,
                    'pipes' => $pipesForProcess,
                    'user_id' => $user->id
                ];
            }
        }

        $this->info("Waiting for all concurrent processes to finish...");
        
        $successes = 0;
        $failures = 0;
        
        foreach ($processes as $p) {
            $output = stream_get_contents($p['pipes'][1]);
            fclose($p['pipes'][0]);
            fclose($p['pipes'][1]);
            fclose($p['pipes'][2]);
            $return_value = proc_close($p['process']);
            
            if ($return_value === 0) {
                $successes++;
            } else {
                $failures++;
            }
        }
        $this->info("Checkout Race Complete: $successes succeeded, $failures failed (expected due to stock limit).");

        // 3. Wait for Queue Workers (simulated by sleep if running sync queue, but we will run the queue)
        $this->info("\n[3] Waiting for queue to process pipelines...");
        sleep(5);

        // 4. Webhook Chaos Simulation
        $this->info("\n[4] Simulating Webhook Chaos (Duplicate & Out-of-Order Events)...");
        $orders = Order::where('status', 'pending')->get();
        foreach ($orders as $order) {
            $payment = Payment::where('order_id', $order->id)->first();
            
            // Randomly spam PaymentPaid multiple times
            $spamCount = rand(2, 5);
            $this->info("Spamming PaymentPaid $spamCount times for Order: {$order->order_number}");
            for ($i = 0; $i < $spamCount; $i++) {
                event(new PaymentPaid($order, $payment));
            }
            
            // Inject an out-of-order PaymentFailed after Paid
            $this->info("Injecting PaymentFailed out-of-order for Order: {$order->order_number}");
            event(new PaymentFailed($order));
        }
        
        sleep(3); // Give time for sync events

        // 5. Final Report & Assertions
        $this->info("\n--- FINAL ASSERTIONS REPORT ---");
        $product->refresh();
        
        $activeOrders = Order::whereIn('status', ['pending', 'paid'])->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        $this->info("Active Orders (Paid/Pending): {$activeOrders}");
        $this->info("Cancelled/Failed Orders: {$cancelledOrders}");
        $this->info("Final Stock: {$product->stock}");

        // Ledger Consistency Check
        $soldStock = \App\Models\OrderItem::whereHas('order', function ($q) {
            $q->whereIn('status', ['pending', 'paid']);
        })->sum('quantity');
        
        $expectedStock = 5 - $soldStock;
        $stockConsistent = ($product->stock === $expectedStock);
        $this->info("Ledger Consistency: " . ($stockConsistent ? 'PASS' : 'FAIL') . " (Sold: {$soldStock}, Remaining: {$product->stock}, Expected Remaining: {$expectedStock})");

        // Stock Movement Consistency
        $movementsOut = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'OUT')->sum('quantity');
        $movementsReturn = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'RETURN')->sum('quantity');
        
        $netMovements = $movementsOut - $movementsReturn;
        $movementConsistent = ($netMovements === (int)$soldStock);
        $this->info("Movement Consistency: " . ($movementConsistent ? 'PASS' : 'FAIL') . " (Net OUT: {$netMovements}, Actual Sold: {$soldStock})");

        // Job Idempotency
        $dupJobs = \App\Models\JobExecution::select('job_name', 'order_id')
            ->groupBy('job_name', 'order_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
            
        $this->info("Duplicate Job Executions Detected: {$dupJobs} (Expected: 0)");

        $verdict = ($stockConsistent && $movementConsistent && $dupJobs === 0)
            ? "PASS - CHAOS-RESILIENT (SAFE FOR PRODUCTION)" 
            : "FAIL - SYSTEM IS VULNERABLE";
            
        $this->info("\nFINAL VERDICT: $verdict");
    }
}

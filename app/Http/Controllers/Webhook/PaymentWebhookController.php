<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming Midtrans Webhook.
     */
    public function handleMidtrans(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $this->paymentService->handleCallback($payload);
            
            return response()->json(['message' => 'OK']);
        } catch (Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage(), ['payload' => $request->all()]);
            
            // Jika error karena payment tidak ditemukan (seperti saat Midtrans mengirim Test Notification),
            // kita harus membalas dengan HTTP 200 agar Midtrans menganggap test berhasil dan tidak melakukan retry berulang kali.
            if (str_contains($e->getMessage(), 'Payment not found') || str_contains($e->getMessage(), 'Invalid payload')) {
                return response()->json(['message' => 'OK, ignored (test or missing order)'], 200);
            }
            
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

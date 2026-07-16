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
            // Midtrans webhook requires 200/400.
            // 400 is returned for bad signature or invalid data to stop it but show failure.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

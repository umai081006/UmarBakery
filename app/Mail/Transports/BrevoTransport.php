<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoTransport extends AbstractTransport
{
    protected string $key;

    public function __construct(string $key)
    {
        parent::__construct();
        $this->key = $key;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();
        
        $from = $email->getFrom()[0] ?? null;
        if (!$from) {
            throw new \Exception('BrevoTransport requires a sender.');
        }

        $payload = [
            'sender' => [
                'name' => $from->getName() ?: config('mail.from.name'),
                'email' => $from->getAddress() ?: config('mail.from.address'),
            ],
            'to' => [],
            'subject' => $email->getSubject(),
        ];

        // Only add HTML or text if present
        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = $html;
        } elseif ($text = $email->getTextBody()) {
            $payload['textContent'] = $text;
        }

        foreach ($email->getTo() as $to) {
            $payload['to'][] = [
                'name' => $to->getName() ?: $to->getAddress(),
                'email' => $to->getAddress(),
            ];
        }

        $response = Http::withHeaders([
            'api-key' => $this->key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->timeout(15)
        ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $response->body();
            
            Log::error('Brevo API Error', [
                'status' => $response->status(),
                'message' => $errorMessage,
                'code' => $errorData['code'] ?? null,
            ]);
            
            throw new \Exception('Brevo API Error: ' . $errorMessage);
        }
    }
    
    public function __toString(): string
    {
        return 'brevo';
    }
}

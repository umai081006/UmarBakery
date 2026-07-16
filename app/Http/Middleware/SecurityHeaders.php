<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders Middleware
 *
 * Adds security-related HTTP response headers to all web responses.
 *
 * CSP is built to be compatible with:
 *  - Vite (local dev HMR + production)
 *  - AlpineJS (inline event handlers require 'unsafe-inline')
 *  - Google Fonts
 *  - Cloudinary (image CDN)
 *  - Midtrans Snap (payment popup)
 *
 * NOTE: 'unsafe-inline' in script-src is required because AlpineJS
 * uses inline x-on:click / @click directives on HTML elements.
 * Removing it would break all Alpine-powered UI interactions.
 *
 * 'unsafe-eval' is required by Midtrans Snap.js which dynamically
 * evaluates scripts for its popup payment frame.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apply headers to all standard Symfony responses.
        // This covers HTML, JSON, and Redirect responses safely.
        // StreamedResponse and BinaryFileResponse also have a headers bag,
        // so this is safe across all response types in Laravel.
        if (! ($response instanceof \Symfony\Component\HttpFoundation\Response)) {
            return $response;
        }

        // -------------------------------------------------------
        // Content-Security-Policy
        // Whitelisted per actual external dependencies in layouts.
        // -------------------------------------------------------
        $csp = implode('; ', [
            // Scripts: self + Vite HMR (localhost) + Midtrans Snap
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://app.midtrans.com https://api.midtrans.com https://app.sandbox.midtrans.com",

            // Styles: self + inline Tailwind/Alpine + Google Fonts
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",

            // Fonts: Google Fonts static files
            "font-src 'self' https://fonts.gstatic.com data:",

            // Images: self + Cloudinary CDN + data URIs
            "img-src 'self' data: blob: https://res.cloudinary.com https://*.cloudinary.com",

            // Frames: Midtrans Snap payment popup
            "frame-src https://app.midtrans.com https://api.midtrans.com https://app.sandbox.midtrans.com",

            // Connections: self + Midtrans API + Vite HMR websocket (dev)
            "connect-src 'self' https://app.midtrans.com https://api.midtrans.com https://app.sandbox.midtrans.com ws://localhost:5173 wss://localhost:5173",

            // Default fallback
            "default-src 'self'",

            // Disallow object embeds
            "object-src 'none'",

            // Restrict base URI to self
            "base-uri 'self'",

            // Only allow form submissions to self
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        // -------------------------------------------------------
        // Clickjacking protection
        // -------------------------------------------------------
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // -------------------------------------------------------
        // MIME sniffing protection
        // -------------------------------------------------------
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // -------------------------------------------------------
        // Referrer Policy: send origin only, not full URL path
        // -------------------------------------------------------
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // -------------------------------------------------------
        // Permissions Policy: disable powerful browser features
        // not needed by this application.
        // -------------------------------------------------------
        $response->headers->set('Permissions-Policy', implode(', ', [
            'geolocation=()',
            'camera=()',
            'microphone=()',
            'payment=(self "https://app.midtrans.com")',
        ]));

        // -------------------------------------------------------
        // XSS Protection (legacy browsers)
        // -------------------------------------------------------
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}

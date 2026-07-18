<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // EMAIL ENUMERATION PROTECTION & PROVIDER ERROR HANDLING:
        // Always return the same generic success message regardless of whether
        // the email exists in our database or not, and regardless of mail provider failures.
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Password reset email delivery failed', [
                'provider' => config('mail.default'),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                // Do not log the full email to protect PII, only log the domain for debug
                'recipient_domain' => substr(strrchr($request->email, "@"), 1)
            ]);
        }

        return back()->with('status', __('Jika email tersebut terdaftar, kami telah mengirimkan instruksi reset password.'));
    }
}

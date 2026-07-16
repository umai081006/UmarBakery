@php
    $layout = auth()->user()->role === 'admin' || auth()->user()->role === 'owner' ? 'layouts.admin' : 'layouts.customer';
@endphp
@extends($layout)

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <div>
        <h1 class="text-3xl font-serif text-cocoa tracking-tight">My Profile</h1>
        <p class="text-sm font-sans text-cocoa/50 mt-2">Manage your account settings, password, and security preferences.</p>
    </div>

    <div class="space-y-8">
        <div class="bg-white rounded-4xl border border-dough/30 shadow-soft p-8 sm:p-10">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white rounded-4xl border border-dough/30 shadow-soft p-8 sm:p-10">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white rounded-4xl border border-dough/30 shadow-soft p-8 sm:p-10">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium overrides for default Breeze forms inside profile */
    header h2 { font-family: 'Playfair Display', serif; color: #4A3B32 !important; font-size: 1.5rem !important; }
    header p { font-family: 'Inter', sans-serif; color: #4A3B32; opacity: 0.7; }
    label { font-family: 'Space Grotesk', monospace; font-size: 0.75rem !important; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; color: #4A3B32 !important; }
    input { border-radius: 1rem !important; border-color: rgba(220, 210, 198, 0.5) !important; background-color: rgba(24DF, 246, 240, 0.5) !important; }
    input:focus { border-color: #D4A373 !important; box-shadow: 0 0 0 2px rgba(212, 163, 115, 0.2) !important; background-color: white !important; }
    button[type="submit"], x-primary-button { background-color: #4A3B32 !important; color: white !important; border-radius: 9999px !important; padding: 0.75rem 2rem !important; font-family: 'Space Grotesk', monospace !important; text-transform: none !important; font-weight: 600 !important; letter-spacing: normal !important; transition: all 0.3s !important; }
    button[type="submit"]:hover, x-primary-button:hover { background-color: #D4A373 !important; transform: translateY(-1px); box-shadow: 0 4px 14px 0 rgba(0,0,0,0.1); }
    x-danger-button { background-color: #EF4444 !important; border-radius: 9999px !important; }
</style>
@endsection

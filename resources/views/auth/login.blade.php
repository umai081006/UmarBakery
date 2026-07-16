<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-serif text-cocoa">Welcome Back</h2>
        <p class="text-sm text-cocoa/50 font-sans mt-2">Sign in to continue your artisan experience.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <div class="relative flex items-center justify-center">
                    <input id="remember_me" type="checkbox" class="peer appearance-none w-5 h-5 border border-dough rounded-md checked:bg-caramel checked:border-caramel transition-colors cursor-pointer" name="remember">
                    <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="ms-3 text-sm text-cocoa/70 font-sans group-hover:text-cocoa transition-colors">Remember Me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-mono font-semibold text-caramel hover:text-cocoa transition-colors" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-4 rounded-full shadow-soft hover:shadow-float transition-all duration-300 active:scale-95 text-sm mt-4">
            Sign In
        </button>
    </form>

    <div class="mt-8 text-center border-t border-dough/30 pt-6">
        <p class="text-sm text-cocoa/50 font-sans">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-mono font-semibold text-caramel hover:text-cocoa transition-colors ml-1">Create Account</a>
        </p>
    </div>
</x-guest-layout>

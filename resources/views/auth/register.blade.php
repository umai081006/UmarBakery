<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-serif text-cocoa">Create Account</h2>
        <p class="text-sm text-cocoa/50 font-sans mt-2">Join us for a premium artisan experience.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="John Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Phone Number</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required autocomplete="tel" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="+62 812 3456 7890">
            <x-input-error :messages="$errors->get('phone')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs font-mono text-strawberry" />
        </div>

        <button type="submit" class="w-full bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-4 rounded-full shadow-soft hover:shadow-float transition-all duration-300 active:scale-95 text-sm mt-4">
            Create Account
        </button>
    </form>

    <div class="mt-8 text-center border-t border-dough/30 pt-6">
        <p class="text-sm text-cocoa/50 font-sans">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-mono font-semibold text-caramel hover:text-cocoa transition-colors ml-1">Sign In here</a>
        </p>
    </div>
</x-guest-layout>

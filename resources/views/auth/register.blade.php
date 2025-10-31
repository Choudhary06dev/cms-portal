<x-guest-layout>
    <div style="max-width: 420px; margin: 32px auto; background: rgba(15,23,42,0.6); border:1px solid rgba(59,130,246,0.2); border-radius: 12px; padding: 20px; box-shadow: 0 8px 24px rgba(2,6,23,0.4);">
    <form method="POST" action="{{ route('register') }}" style="margin:0;">
        @csrf

        <!-- Username -->
        <div class="form-group" style="margin-bottom:12px;">
            <label for="username" class="form-label">
                <i data-feather="user" class="inline w-4 h-4 mr-2"></i>Username
            </label>
            <input id="username" class="form-input" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Choose a username" style="width:100%; padding:.55rem .75rem; font-size:.92rem; border-radius:8px;" />
            @error('username')
                <div class="error-message" style="color:#ef4444; font-size:.82rem; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group" style="margin-bottom:12px;">
            <label for="email" class="form-label">
                <i data-feather="mail" class="inline w-4 h-4 mr-2"></i>Email Address
            </label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Enter your email" style="width:100%; padding:.55rem .75rem; font-size:.92rem; border-radius:8px;" />
            @error('email')
                <div class="error-message" style="color:#ef4444; font-size:.82rem; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group" style="margin-bottom:12px;">
            <label for="password" class="form-label">
                <i data-feather="lock" class="inline w-4 h-4 mr-2"></i>Password
            </label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" style="width:100%; padding:.55rem .75rem; font-size:.92rem; border-radius:8px;" />
            @error('password')
                <div class="error-message" style="color:#ef4444; font-size:.82rem; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group" style="margin-bottom:16px;">
            <label for="password_confirmation" class="form-label">
                <i data-feather="lock" class="inline w-4 h-4 mr-2"></i>Confirm Password
            </label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" style="width:100%; padding:.55rem .75rem; font-size:.92rem; border-radius:8px;" />
            @error('password_confirmation')
                <div class="error-message" style="color:#ef4444; font-size:.82rem; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom:8px;">
            <button type="submit" class="btn-primary" style="width:100%; padding:.6rem 1rem; font-size:.95rem; border-radius:8px;">
                <i data-feather="user-plus" class="inline w-4 h-4 mr-2"></i>Create Account
            </button>
        </div>

        <div class="text-center" style="margin-top:10px;">
            <p class="text-sm text-gray-600" style="font-size:.9rem;">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
    </div>
</x-guest-layout>

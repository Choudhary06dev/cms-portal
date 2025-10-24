<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i data-feather="mail" class="inline w-4 h-4 mr-2"></i>Email Address
            </label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email" />
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                <i data-feather="lock" class="inline w-4 h-4 mr-2"></i>Password
            </label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-primary">
                <i data-feather="log-in" class="inline w-4 h-4 mr-2"></i>Sign In
            </button>
        </div>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-gray-800">
                    Forgot your password?
                </a>
            @endif
        </div>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Sign up here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

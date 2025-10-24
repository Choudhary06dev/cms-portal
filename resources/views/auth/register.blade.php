<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username -->
        <div class="form-group">
            <label for="username" class="form-label">
                <i data-feather="user" class="inline w-4 h-4 mr-2"></i>Username
            </label>
            <input id="username" class="form-input" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Choose a username" />
            @error('username')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i data-feather="mail" class="inline w-4 h-4 mr-2"></i>Email Address
            </label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Enter your email" />
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                <i data-feather="lock" class="inline w-4 h-4 mr-2"></i>Password
            </label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" />
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i data-feather="lock" class="inline w-4 h-4 mr-2"></i>Confirm Password
            </label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
            @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn-primary">
                <i data-feather="user-plus" class="inline w-4 h-4 mr-2"></i>Create Account
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

@extends('frontend.layouts.app')

@section('title', 'Change Password')

@section('content')
    <style>
        .password-page-wrapper {
            min-height: calc(100vh - 200px);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .password-page-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,213.3C672,224,768,224,864,208C960,192,1056,160,1152,154.7C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .password-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .password-card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .password-card-header h5 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .password-card-header .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .password-card-header .icon-circle i {
            font-size: 2.5rem;
        }

        .password-card-body {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f093fb;
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
        }

        .btn-update-password {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            padding: 14px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);
        }

        .btn-update-password:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.6);
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        }

        .alert-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 15px 20px;
            font-weight: 500;
        }
    </style>

    <div class="password-page-wrapper">
        <div class="password-card">
            <div class="password-card-header">
                <div class="icon-circle">
                    <i class="fas fa-key"></i>
                </div>
                <h5>Change Password</h5>
            </div>
            <div class="password-card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('frontend.password.update') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                            id="current_password" name="current_password" required>
                        @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            required>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-update-password">
                            <i class="fas fa-lock me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
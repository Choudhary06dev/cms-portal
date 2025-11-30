@extends('frontend.layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        .profile-header-bg {
            height: 300px;
            background-image: url('https://img.freepik.com/premium-photo/dark-blue-ocean-surface-seen-from-underwater_629685-6504.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .profile-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .avatar-ring {
            background: white;
            padding: 6px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .info-card {
            transition: all 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #0ea5e9;
        }
    </style>
@endpush

@section('content')
    <!-- Header Background -->
    <div class="profile-header-bg w-full">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/20"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white tracking-wide">User Profile</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 -mt-40 relative z-10 mb-12">
        <div class="max-w-6xl mx-auto">
            <!-- Main Profile Card -->
            <div class="profile-card rounded-2xl overflow-hidden mb-6">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                        <!-- Avatar Section -->
                        <div class="flex-shrink-0">
                            <div class="w-40 h-40 rounded-full avatar-ring">
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 flex items-center justify-center text-white text-5xl font-bold shadow-inner">
                                    {{ strtoupper(substr(Auth::guard('frontend')->user()->username ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                        </div>

                        <!-- User Info Section -->
                        <div class="flex-grow text-center md:text-left w-full">
                            <h2 class="text-4xl font-bold text-gray-800 mb-2">{{ Auth::guard('frontend')->user()->username }}</h2>
                            <p class="text-blue-600 font-semibold text-lg mb-6 flex items-center justify-center md:justify-start gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                {{ Auth::guard('frontend')->user()->email }}
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                                <div class="info-card bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border border-blue-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider">Role</label>
                                            <p class="text-gray-900 font-bold text-lg">{{ Auth::guard('frontend')->user()->role ?? 'User' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-card bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl border border-green-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-green-700 uppercase tracking-wider">Member Since</label>
                                            <p class="text-gray-900 font-bold text-lg">{{ Auth::guard('frontend')->user()->created_at ? Auth::guard('frontend')->user()->created_at->format('M Y') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-card bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl border border-purple-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-purple-700 uppercase tracking-wider">Status</label>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-500 text-white">
                                                ● Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Account Details -->
                <div class="profile-card rounded-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                            </svg>
                            Account Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-semibold">Username:</span>
                            <span class="text-gray-900 font-bold">{{ Auth::guard('frontend')->user()->username }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-semibold">Email Address:</span>
                            <span class="text-gray-900 font-bold">{{ Auth::guard('frontend')->user()->email }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-semibold">Account Type:</span>
                            <span class="text-blue-600 font-bold">Frontend User</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 font-semibold">User ID:</span>
                            <span class="text-gray-900 font-bold">#{{ Auth::guard('frontend')->user()->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Activity Summary -->
                <div class="profile-card rounded-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                            Activity Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="stat-card p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-semibold">Last Login:</span>
                                <span class="text-gray-900 font-bold">{{ now()->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="stat-card p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-semibold">Account Created:</span>
                                <span class="text-gray-900 font-bold">{{ Auth::guard('frontend')->user()->created_at ? Auth::guard('frontend')->user()->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="stat-card p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-semibold">Profile Updated:</span>
                                <span class="text-gray-900 font-bold">{{ Auth::guard('frontend')->user()->updated_at ? Auth::guard('frontend')->user()->updated_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="stat-card p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-semibold">Account Status:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    ✓ Verified & Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="profile-card rounded-2xl overflow-hidden">
                <div class="px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-gray-600 text-sm">
                        <p class="font-semibold">Need to update your information?</p>
                        <p class="text-xs">Contact your administrator for profile changes.</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('frontend.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

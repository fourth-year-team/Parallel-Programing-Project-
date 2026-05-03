@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Reset your password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your email and we’ll send a link to reset your password.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500" />
                @error('email')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700">Email Password Reset Link</button>
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Remembered your password?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Login</a>
        </p>
    </div>
</div>
@endsection

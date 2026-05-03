@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Verify your email</h2>
            <p class="mt-2 text-sm text-gray-600">A verification link has been sent to your email address.</p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-green-700">A new verification link has been sent to your email address.</div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700">Resend verification email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-gray-200 px-4 py-3 text-gray-900 font-semibold hover:bg-gray-300">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection

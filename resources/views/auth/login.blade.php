@extends('layouts.auth')
@section('content')
<div class="flex justify-center items-center min-h-screen bg-white">
    <div class="w-full max-w-md p-8 space-y-6">
        <div class="text-center">
            <img src="{{ asset('images/logo-dkp.png') }}" class="h-24 mx-auto mb-4">
            <h2 class="text-xl font-semibold">Log in to your account</h2>
            <p class="text-gray-500 text-sm">Welcome back! Please enter your details.</p>
        </div>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input name="email" type="email" placeholder="Enter your email" required class="w-full border px-3 py-2 rounded">
            <input name="password" type="password" placeholder="Password" required class="w-full border px-3 py-2 rounded">
            <div class="flex justify-between items-center text-sm">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#" class="text-blue-600">Forgot password?</a>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Sign in</button>
            <a href="#" class="w-full flex justify-center border py-2 rounded">Sign in with Google</a>
        </form>
        <p class="text-center text-sm">Don't have an account? <a href="{{ route('register') }}" class="text-blue-600">Sign up</a></p>
    </div>
</div>
@endsection

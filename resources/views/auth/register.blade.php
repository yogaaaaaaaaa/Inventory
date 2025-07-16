@extends('layouts.auth')
@section('content')
<div class="flex justify-center items-center min-h-screen bg-white">
    <div class="w-full max-w-md p-8 space-y-6">
        <div class="text-center">
            <img src="{{ asset('images/logo-dkp.png') }}" class="h-24 mx-auto mb-4">
            <h2 class="text-xl font-semibold">Create an account</h2>
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <input name="name" type="text" placeholder="Enter your name" required class="w-full border px-3 py-2 rounded">
            <input name="email" type="email" placeholder="Enter your email" required class="w-full border px-3 py-2 rounded">
            <input name="password" type="password" placeholder="Create a password" required class="w-full border px-3 py-2 rounded">
            <input name="password_confirmation" type="password" placeholder="Confirm password" required class="w-full border px-3 py-2 rounded">
            <div class="form-group">
            <label for="role">Daftar Sebagai</label>
                 <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="kepala_upt">Kepala UPT</option>
                    <option value="kepala_dinas">Kepala Dinas</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Get started</button>
            <a href="#" class="w-full flex justify-center border py-2 rounded">Sign up with Google</a>
        </form>
        <p class="text-center text-sm">Already have an account? <a href="{{ route('login') }}" class="text-blue-600">Log in</a></p>
    </div>
</div>
@endsection

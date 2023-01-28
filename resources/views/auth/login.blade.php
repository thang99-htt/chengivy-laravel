@extends('layouts.default')

@section('content')
<x-guest-layout>
    <x-auth-card>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="account-login">
            <form method="POST" action="{{ route('login') }}" class="card login-form">
                @csrf
    
                <div class="title">
                    <h3>Login Now</h3>
                    <p>
                        You can login using your social media account or email
                        address.
                    </p>
                </div>
    
                <div class="social-login">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-12">
                            <a class="btn facebook-btn" href="javascript:void(0)"><i class="lni lni-facebook-filled"></i> Facebook
                                login</a>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <a class="btn twitter-btn" href="javascript:void(0)"><i class="lni lni-twitter-original"></i> Twitter
                                login</a>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <a class="btn google-btn" href="javascript:void(0)"><i class="lni lni-google"></i> Google login</a>
                        </div>
                    </div>
                </div>
                <div class="alt-option">
                    <span>Or</span>
                </div>
                
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <!-- Email Address -->
                <div>
                    <x-label for="email" :value="__('Email')" />
    
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>
    
                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" />
    
                    <x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                </div>
    
                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>
    
                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
    
                    <x-button class="ml-3">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
@endsection

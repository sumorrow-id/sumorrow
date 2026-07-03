@extends('layouts.app')

@section('content')
    {{-- FontAwesome untuk icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="pt-32 pb-20 bg-[#F8F9FA] min-h-screen flex items-center justify-center">
        
        <div class="w-[95%] max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="p-8 sm:p-14">

                <h1 class="text-4xl sm:text-5xl font-bold text-[#094174] mb-2 text-center tracking-tight">
                    {{ __('auth.reset_password_heading') }}
                </h1>
                <p class="text-gray-400 text-sm mb-10 text-center font-medium">
                    {{ __('auth.reset_password_subheading') }}
                </p>

                <!-- Reset Password Form -->
                <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Input Email (readonly) --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-regular fa-envelope text-lg"></i>
                        </div>
                        <input type="email" name="email" value="{{ $email }}" required readonly
                            class="w-full bg-gray-100 border border-gray-200 rounded-2xl py-4 pl-14 pr-4 text-m text-gray-500 font-medium cursor-not-allowed">
                    </div>

                    {{-- Input Password Baru --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-solid fa-lock text-lg"></i>
                        </div>
                        
                        <input type="password" id="password" name="password" required autofocus
                            class="w-full bg-[#F8F9FA] border @error('password') border-red-500 @else border-gray-100 @enderror rounded-2xl py-4 pl-14 pr-14 focus:outline-none @error('password') focus:border-red-500 focus:ring-1 focus:ring-red-500 @else focus:border-[#094174] focus:ring-1 focus:ring-[#094174] @enderror transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            placeholder="{{ __('auth.reset_password_password_placeholder') }}">

                        <button type="button" id="toggleBtn" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-[#094174] transition-colors focus:outline-none">
                            <i class="fa-solid fa-eye-slash text-lg" id="eyeIcon"></i>
                        </button>
                    </div>

                    @if ($errors->has('password'))
                        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-lg">
                            {{ $errors->first('password') }}
                        </div>
                    @endif

                    {{-- Input Konfirmasi Password --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-solid fa-lock text-lg"></i>
                        </div>
                        
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full bg-[#F8F9FA] border @error('password_confirmation') border-red-500 @else border-gray-100 @enderror rounded-2xl py-4 pl-14 pr-14 focus:outline-none @error('password_confirmation') focus:border-red-500 focus:ring-1 focus:ring-red-500 @else focus:border-[#094174] focus:ring-1 focus:ring-[#094174] @enderror transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            placeholder="{{ __('auth.reset_password_confirm_placeholder') }}">

                        <button type="button" id="toggleBtnConfirm" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-[#094174] transition-colors focus:outline-none">
                            <i class="fa-solid fa-eye-slash text-lg" id="eyeIconConfirm"></i>
                        </button>
                    </div>

                    @if ($errors->has('password_confirmation'))
                        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-lg">
                            {{ $errors->first('password_confirmation') }}
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-4 rounded-2xl shadow-lg shadow-[#094174]/20 transition transform active:scale-[0.97] text-m tracking-wide mt-8">
                        {{ __('auth.reset_password_submit') }}
                    </button>
                </form>

            </div>

        </div>

    </div>
@endsection

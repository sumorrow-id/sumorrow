@extends('layouts.app')

@section('content')
    {{-- FontAwesome untuk icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <div class="pt-32 pb-20 bg-[#F8F9FA] min-h-screen flex items-center justify-center">
        
        <div class="w-[95%] max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="p-8 sm:p-14">

                <h1 class="text-4xl sm:text-5xl font-bold text-[#094174] mb-2 text-center tracking-tight">
                    Lupa Password?
                </h1>
                <p class="text-gray-400 text-sm mb-10 text-center font-medium">
                    Masukkan email Anda untuk menerima link reset password
                </p>

                <!-- Forgot Password Form -->
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if (session('message'))
                        <div class="bg-green-500/10 border border-green-500 text-green-700 p-4 rounded-lg mb-6">
                            <div class="flex gap-3">
                                <i class="fa-solid fa-check-circle text-lg mt-0.5 flex-shrink-0"></i>
                                <span>{{ session('message') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Input Email --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-regular fa-envelope text-lg"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-[#F8F9FA] border @error('email') border-red-500 @else border-gray-100 @enderror rounded-2xl py-4 pl-14 pr-4 focus:outline-none @error('email') focus:border-red-500 focus:ring-1 focus:ring-red-500 @else focus:border-[#094174] focus:ring-1 focus:ring-[#094174] @enderror transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            placeholder="Masukkan email Anda">
                    </div>

                    @if ($errors->has('email'))
                        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-lg">
                            <div class="flex gap-3">
                                <i class="fa-solid fa-exclamation-circle text-lg mt-0.5 flex-shrink-0"></i>
                                <span>{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                    @endif

                    <button type="submit" 
                        class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-4 rounded-2xl shadow-lg shadow-[#094174]/20 transition transform active:scale-[0.97] text-m tracking-wide mt-8">
                        Kirim Link Reset Password
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="text-center mt-8">
                    <p class="text-gray-400 text-sm">
                        Ingat password Anda?
                        <a href="{{ route('showLogin') }}" class="text-[#094174] font-bold hover:underline">
                            Kembali ke Login
                        </a>
                    </p>
                </div>

            </div>

        </div>

    </div>
@endsection

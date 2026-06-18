@extends('layouts.app')

@section('content')
    {{-- FontAwesome untuk icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Ini buat hps icon mata bawaan Microsoft Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
    
    <div class="pt-32 pb-20 bg-[#F8F9FA] min-h-screen flex items-center justify-center">
        
        <div class="w-[95%] max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-150 border border-gray-100">
            
            <!-- Left Side: Image Section -->
            <div class="hidden md:block md:w-1/2 relative">
                <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80" 
                    alt="Mountain Background" 
                    class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/5"></div>
            </div>

            <!-- Right Side: Form Section -->
            <div class="w-full md:w-1/2 bg-white p-8 sm:p-14 flex flex-col justify-center">

                <h1 class="text-4xl sm:text-5xl font-bold text-[#094174] mb-2 text-center md:text-left tracking-tight">
                    Sign In
                </h1>
                <p class="text-gray-400 text-sm mb-10 text-center md:text-left font-medium">Ready for your next summit?</p>

                <!-- Login Form -->
                <form action="{{ url('/login') }}" method="POST" class="space-y-6 mb-8">
                    @csrf
                    
                    @if (session('success'))
                        <div class="bg-green-500/10 border border-green-500 text-green-500 p-4 rounded-lg mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    {{-- Input Email --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-regular fa-envelope text-lg"></i>
                        </div>
                        <input type="email" name="email" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-4 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            placeholder="Enter your email">
                    </div>

                    {{-- Input Password  --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-solid fa-lock text-lg"></i>
                        </div>
                        
                        <input type="password" id="password" name="password" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-14 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            placeholder="Enter your password">

                        <button type="button" id="toggleBtn" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-[#094174] transition-colors focus:outline-none">
                            <i class="fa-solid fa-eye-slash text-lg" id="eyeIcon"></i>
                        </button>
                    </div>
                    @if ($errors->any())
                        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-lg mb-6">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" 
                                value="1"
                                class="w-4 h-4 rounded cursor-pointer"
                                style="accent-color: #094174;">
                            <label for="remember" class="ml-2 text-sm text-gray-400 cursor-pointer select-none">
                                Remember Me
                            </label>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('password.request') }}" class="text-[#094174] text-sm font-bold hover:underline opacity-80">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-4 rounded-2xl shadow-lg shadow-[#094174]/20 transition transform active:scale-[0.97] text-m tracking-wide mt-2">
                        Login to Account
                    </button>
                </form>

                <!-- Divider (OR) -->
                <div class="flex items-center gap-3 mb-6"> {{-- Jarak bawah dikurangi dikit --}}
                    <div class="flex-1 border-t-2 border-dashed border-gray-200"></div>
                    <span class="text-gray-300 text-xs font-bold tracking-widest uppercase">Or</span>
                    <div class="flex-1 border-t-2 border-dashed border-gray-200"></div>
                </div>

                <!-- Google Login Button -->
                <a href="{{ route('google.redirect') }}" class="w-full bg-white text-[#1a2b4c] font-bold py-4 px-6 rounded-2xl shadow-sm flex items-center justify-center gap-3 hover:bg-gray-50 transition border border-gray-100 active:scale-[0.98]">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-8 h-8" alt="Google">
                    <span class="text-m">Continue with Google</span>
                </a>

                <!-- Create Account Link -->
                <p class="text-center text-sm text-gray-400 mt-10 font-medium">
                    Don't have account yet? <a href="{{route('register')}}" class="text-[#094174] font-bold hover:underline">Create an account</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Script untuk Validasi Email dan Pw --}}
    <script>
        const emailInput = document.querySelector('input[type="email"]');

        emailInput.addEventListener('invalid', function(e){
            // cek kosong atau salah email
            if(emailInput.value === ''){
                emailInput.setCustomValidity('Email tidak boleh kosong');
            }
            else{
                emailInput.setCustomValidity('Format email salah');
            }
        });

        emailInput.addEventListener('input', function(){
            emailInput.setCustomValidity('');
        });

        const toggleBtn = document.querySelector('#toggleBtn');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        toggleBtn.addEventListener('click', function () {
            // Toggle tipe input
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            // Toggle icon mata
            eyeIcon.classList.toggle('fa-eye-slash', !isPassword);
            eyeIcon.classList.toggle('fa-eye', isPassword);
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')
    {{-- FontAwesome untuk icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
    
    <div class="pt-32 pb-20 bg-[#F8F9FA] min-h-screen flex items-center justify-center">
        
        <div class="w-[95%] max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-150 border border-gray-100">
            
            <!-- Left Side: Image Section (Sama dengan Login) -->
            <div class="hidden md:block md:w-1/2 relative">
                <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80" 
                    alt="Mountain Background" 
                    class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/5"></div>
            </div>

            <!-- Right Side: Form Section -->
            <div class="w-full md:w-1/2 bg-white p-8 sm:p-14 flex flex-col justify-center">

                <h1 class="text-4xl sm:text-5xl font-bold text-[#094174] mb-2 text-center md:text-left tracking-tight">
                    Create <span class="text-[#1a2b4c]/70">Account</span>
                </h1>
                <p class="text-gray-400 text-sm mb-10 text-center md:text-left font-medium">Join the Sumorrow community today.</p>

                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200">
                        <ul class="text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ rtrim($error, '.') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Form Register -->
                <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Input Full Name --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-regular fa-user text-lg"></i>
                        </div>
                        <input type="text" name="username" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-4 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            value="{{ old('username') }}"
                            placeholder="Full Name">
                    </div>

                    {{-- Input Email --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-regular fa-envelope text-lg"></i>
                        </div>
                        <input type="email" id="regEmail" name="email" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-4 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            value="{{ old('email') }}"
                            placeholder="Email Address">
                    </div>

                    {{-- Input Password --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-solid fa-lock text-lg"></i>
                        </div>
                        <input type="password" id="regPassword" name="password" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-14 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            value="{{ old('password') }}"
                            placeholder="Create Password">
                        <button type="button" id="regToggleBtn" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-[#094174] focus:outline-none">
                            <i class="fa-solid fa-eye-slash text-lg" id="regEyeIcon"></i>
                        </button>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-[#094174]">
                            <i class="fa-solid fa-shield-halved text-lg"></i>
                        </div>
                        <input type="password" id="confirmPassword" name="password_confirmation" required
                            class="w-full bg-[#F8F9FA] border border-gray-100 rounded-2xl py-4 pl-14 pr-14 focus:outline-none focus:border-[#094174] focus:ring-1 focus:ring-[#094174] transition placeholder:text-gray-400 text-m text-[#1a2b4c] font-medium"
                            value="{{ old('password') }}"
                            placeholder="Confirm Password">
                        <button type="button" id="confirmToggleBtn" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-[#094174] focus:outline-none">
                            <i class="fa-solid fa-eye-slash text-lg" id="confirmEyeIcon"></i> <!-- ID Diubah -->
                        </button>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-4 rounded-2xl shadow-lg shadow-[#094174]/20 transition transform active:scale-[0.97] mt-4 text-m tracking-wide">
                        Create Account
                    </button>

                    <p class="text-center text-sm text-gray-400 mt-8 font-medium">
                        Already have an account? <a href="{{ route('showLogin') }}" class="text-[#094174] font-bold hover:underline">Sign In</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Logic untuk toggle password di page register
        const regToggleBtn = document.querySelector('#regToggleBtn');
        const regPasswordInput = document.querySelector('#regPassword');
        const regEyeIcon = document.querySelector('#regEyeIcon');

        regToggleBtn.addEventListener('click', function () {
            const isPassword = regPasswordInput.getAttribute('type') === 'password';
            regPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
            regEyeIcon.classList.toggle('fa-eye-slash', !isPassword);
            regEyeIcon.classList.toggle('fa-eye', isPassword);
        });

        // Validasi Email Sederhana
        const emailInput = document.querySelector('#regEmail');
        emailInput.addEventListener('invalid', function(e){
            if(emailInput.value === '') emailInput.setCustomValidity('Email tidak boleh kosong');
            else emailInput.setCustomValidity('Format email salah');
        });
        emailInput.addEventListener('input', function(){ emailInput.setCustomValidity(''); });
    
        // Validasi Confirm Password
        const confirmToggleBtn = document.querySelector('#confirmToggleBtn');
        const confirmPasswordInput = document.querySelector('#confirmPassword');
        const confirmEyeIcon = document.querySelector('#confirmEyeIcon');

        confirmToggleBtn.addEventListener('click', function () {
            const isPassword = confirmPasswordInput.getAttribute('type') === 'password';
            confirmPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
            confirmEyeIcon.classList.toggle('fa-eye-slash', !isPassword);
            confirmEyeIcon.classList.toggle('fa-eye', isPassword);
        });
    </script>
@endsection
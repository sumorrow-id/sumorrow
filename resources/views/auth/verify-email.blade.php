@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F8F9FA] px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
        <h2 class="text-2xl font-bold text-[#094174] mb-4">Verify Your Email</h2>
        
        <p class="text-gray-500 mb-8">
            Thanks for signing up to <strong>Sumorrow</strong>! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        </p>

        @if (session('message'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg text-sm font-medium">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="space-y-4">
            <!-- Tombol Kirim Ulang -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-3 rounded-xl transition transform active:scale-95">
                    Resend Verification Email
                </button>
            </form>

            <!-- Tombol Logout (Jika user mau pakai akun lain) -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-[#094174] font-medium transition underline">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    setInterval(function() {
        fetch('/api/check-verification')
            .then(response => response.json())
            .then(data => {
                if (data.verified) {
                    window.location.href = "{{ route('home') }}";
                }
            });
    }, 3000);
</script>
@endsection

<!-- abis regis->verif email->home page -->
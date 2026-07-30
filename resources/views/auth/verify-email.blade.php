@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F8F9FA] px-4" data-redirect-url="{{ route('home') }}">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
        <h2 class="text-2xl font-bold text-[#094174] mb-4">{{ __('auth.verify_email_heading') }}</h2>

        <p class="text-gray-500 mb-8">
            {!! __('auth.verify_email_body', ['app_name' => '<strong>Sumorrow</strong>']) !!}
        </p>

        <div class="space-y-4">
            <!-- Tombol Kirim Ulang -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-[#094174] hover:bg-[#073056] text-white font-bold py-3 rounded-xl transition transform active:scale-95">
                    {{ __('auth.verify_email_resend') }}
                </button>
            </form>

            <!-- Tombol Logout (Jika user mau pakai akun lain) -->
            <form method="POST" action="{{ route('logout') }}" class="confirm-submit-form" data-confirm-title="{{ __('common.confirm_logout_title') }}" data-confirm-message="{{ __('common.confirm_logout_message') }}" data-confirm-label="{{ __('common.confirm_logout_label') }}" data-confirm-variant="danger">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-[#094174] font-medium transition underline">
                    {{ __('auth.verify_email_log_out') }}
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

<!-- abis regis->verif email->home page -->
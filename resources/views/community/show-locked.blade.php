@extends('layouts.app')

{{-- Token gate shown to non-members of a private community. --}}
@section('content')
<div class="w-[95%] sm:w-[90%] max-w-2xl mx-auto pt-24 md:pt-32 pb-12">

    <a href="{{ route('community', ['tab' => 'community']) }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#094174] transition mb-4 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        {{ __('community.back_to_community') }}
    </a>

    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="h-36 sm:h-48 w-full bg-gray-200">
            <img src="{{ $community->bannerImageUrl() }}" alt="{{ __('community.banner_image_label') }}"
                class="w-full h-full object-cover">
        </div>

        <div class="px-4 sm:px-8 pb-8 relative pt-14 text-center">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 w-20 h-20 rounded-2xl border-4 border-white bg-gray-100 shadow-md overflow-hidden">
                <img src="{{ $community->profileImageUrl() }}" alt="{{ __('community.profile_image_label') }}"
                    class="w-full h-full object-cover">
            </div>

            <h1 class="text-xl sm:text-2xl font-extrabold text-[#094174] tracking-tight break-words">
                {{ $community->name }}
            </h1>
            <span class="inline-block mt-1.5 px-2.5 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded-md bg-purple-100 text-purple-700">
                {{ __('community.privacy_private') }}
            </span>

            <div class="max-w-sm mx-auto mt-6">
                <div class="w-14 h-14 bg-blue-50 text-[#094174] rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="font-bold text-base text-[#001E3A]">{{ __('community.private_locked_title') }}</h2>
                <p class="text-xs text-gray-400 mt-1 mb-5">{{ __('community.private_locked_text') }}</p>

                <form method="POST" action="{{ route('community.join', $community) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="join_token" required value="{{ old('join_token') }}"
                        placeholder="{{ __('community.token_placeholder') }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('join_token') ? 'border-red-300' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading text-center font-mono tracking-widest uppercase" />

                    @error('join_token')
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-[#094174] text-white font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                        {{ __('community.join_community_button') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-3xl mx-auto pt-32 pb-16">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#1a2b4c]">{{ __('profile.edit_profile') }}</h1>
            <a href="{{ route('profile') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 confirm-submit-form"
            data-confirm-title="{{ __('profile.confirm_save_title') }}" data-confirm-message="{{ __('profile.confirm_save_message') }}"
            data-confirm-label="{{ __('common.save') }}">
            @csrf
            @method('PATCH')

            <!-- Cover Image section -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('profile.cover_image') }}</label>
                <div class="relative h-48 w-full rounded-2xl overflow-hidden bg-gray-100 group border-2 border-dashed border-gray-200 hover:border-[#094174]/50 transition-colors">
                    @php
                        $cover = $user->cover_url;
                        $coverSrc = $cover ? (str_contains($cover, 'http') ? $cover : asset('storage/' . $cover)) : asset('images/profile/banner.jpeg');
                    @endphp
                    <img src="{{ $coverSrc }}" id="cover-preview" alt="{{ __('profile.cover_preview_alt') }}" class="w-full h-full object-cover">
                    <label for="cover-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ __('profile.change_cover') }}
                        </div>
                    </label>
                    <input type="file" id="cover-upload" name="cover" class="hidden" accept="image/*">
                </div>
            </div>

            <!-- Profile Info and Avatar -->
            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('profile.profile_photo') }}</label>
                    <div class="relative w-32 h-32 rounded-full overflow-hidden bg-gray-100 group border-4 border-white shadow-md">
                        <img src="{{ $user->avatarUrl() }}" id="avatar-preview" alt="{{ __('profile.avatar_preview_alt') }}" class="w-full h-full object-cover">
                        <label for="avatar-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        </label>
                        <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*">
                    </div>
                </div>

                <!-- Username & Status -->
                <div class="flex-grow space-y-5">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('profile.username') }}</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#094174] focus:ring-2 focus:ring-[#094174]/20 outline-none transition-all placeholder:text-gray-400">
                        @error('username')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('profile.email_address') }}</label>
                        <div class="flex items-center gap-3">
                            <input type="email" value="{{ $user->email }}" disabled 
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                            
                            @if($user->hasVerifiedEmail())
                                <div class="flex items-center gap-1.5 px-4 py-2.5 bg-green-50 text-green-700 rounded-xl font-medium text-sm whitespace-nowrap border border-green-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('profile.verified') }}
                                </div>
                            @endif
                        </div>
                        @if(!$user->hasVerifiedEmail())
                            <p class="mt-2 text-sm text-amber-600 flex items-start gap-1.5">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>{{ __('profile.email_unverified') }} <button type="button" onclick="document.getElementById('verify-form').submit()" class="font-bold underline hover:text-amber-700">{{ __('profile.send_verification_link') }}</button></span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="mb-8">
                <label for="bio" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('profile.bio') }}</label>
                <textarea id="bio" name="bio" rows="4" placeholder="{{ __('profile.bio_placeholder') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#094174] focus:ring-2 focus:ring-[#094174]/20 outline-none transition-all resize-y placeholder:text-gray-400">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-500 text-right" id="bio-counter">{{ __('profile.bio_counter', ['count' => 0, 'max' => 500]) }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('profile') }}" class="px-6 py-2.5 rounded-xl font-medium text-gray-600 hover:bg-gray-100 transition-colors">{{ __('common.cancel') }}</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-[#094174] hover:bg-[#105DA3] shadow-md shadow-[#094174]/20 transition-all hover:-translate-y-0.5">{{ __('common.save_changes') }}</button>
            </div>
        </form>
        
        <!-- Hidden form for verifying email -->
        <form id="verify-form" action="{{ route('verification.send') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-3xl mx-auto pt-32 pb-16 px-4 sm:px-8">
    <div class="mb-8">
        <a href="{{ route('profile.posts.index') }}" class="text-sm font-bold text-[#094174] hover:underline flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('profile.back_to_activities') }}
        </a>
        <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] tracking-wide">{{ __('profile.new_activity_heading') }}</h1>
        <p class="text-[#6D8A9F] text-sm md:text-base mt-2">{{ __('profile.new_activity_subtitle') }}</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">
        <form action="{{ route('profile.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.activity_title') }} <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition"
                    placeholder="{{ __('profile.activity_title_placeholder') }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mountain -->
                <div>
                    <label for="mountain_id" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.mountain_optional') }}</label>
                    <select name="mountain_id" id="mountain_id"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] bg-white transition">
                        <option value="">{{ __('profile.select_mountain') }}</option>
                        @foreach($mountains as $mountain)
                            <option value="{{ $mountain->id }}" {{ old('mountain_id') == $mountain->id ? 'selected' : '' }}>
                                {{ $mountain->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Climbing Date -->
                <div>
                    <label for="climbing_date" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.climbing_date_optional') }}</label>
                    <input type="date" name="climbing_date" id="climbing_date" value="{{ old('climbing_date') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition">
                </div>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_days" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.duration_days_optional') }}</label>
                <input type="number" name="duration_days" id="duration_days" min="1" value="{{ old('duration_days') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition"
                    placeholder="{{ __('profile.duration_days_placeholder') }}">
            </div>

            <!-- Content / Body -->
            <div>
                <label for="body" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.expedition_details') }} <span class="text-red-500">*</span></label>
                <textarea name="body" id="body" rows="6" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition resize-y"
                    placeholder="{{ __('profile.expedition_details_placeholder') }}">{{ old('body') }}</textarea>
                <p class="text-xs text-gray-500 mt-2">{{ __('profile.markdown_help') }}</p>
            </div>

            <!-- Images -->
            <div>
                <label for="images" class="block text-sm font-bold text-gray-700 mb-2">{{ __('profile.photos') }}</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#E2E8F0] file:text-[#094174] hover:file:bg-[#CBD5E1] transition">
                <p class="text-xs text-gray-500 mt-2">{{ __('profile.photos_help') }}</p>
                <p id="image-error" class="text-sm text-red-600 mt-2 hidden"></p>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end">
                <button type="submit" id="submit-btn" class="bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-3 px-8 rounded-full transition shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    {{ __('profile.post_activity') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
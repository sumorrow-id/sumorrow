@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-3xl mx-auto pt-32 pb-16 px-4 sm:px-8">
    <div class="mb-8">
        <a href="{{ route('profile.posts.index') }}" class="text-sm font-bold text-[#094174] hover:underline flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Activities
        </a>
        <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] tracking-wide">New Activity</h1>
        <p class="text-[#6D8A9F] text-sm md:text-base mt-2">Log your hiking trip or share your expedition plan.</p>
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
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Activity Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition"
                    placeholder="e.g., Summit Attack to Mt. Rinjani">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mountain -->
                <div>
                    <label for="mountain_id" class="block text-sm font-bold text-gray-700 mb-2">Mountain (Optional)</label>
                    <select name="mountain_id" id="mountain_id" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] bg-white transition">
                        <option value="">-- Select Mountain --</option>
                        @foreach($mountains as $mountain)
                            <option value="{{ $mountain->id }}" {{ old('mountain_id') == $mountain->id ? 'selected' : '' }}>
                                {{ $mountain->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Climbing Date -->
                <div>
                    <label for="climbing_date" class="block text-sm font-bold text-gray-700 mb-2">Climbing Date (Optional)</label>
                    <input type="date" name="climbing_date" id="climbing_date" value="{{ old('climbing_date') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition">
                </div>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_days" class="block text-sm font-bold text-gray-700 mb-2">Duration (Days) (Optional)</label>
                <input type="number" name="duration_days" id="duration_days" min="1" value="{{ old('duration_days') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition"
                    placeholder="e.g., 3">
            </div>

            <!-- Content / Body -->
            <div>
                <label for="body" class="block text-sm font-bold text-gray-700 mb-2">Expedition Details <span class="text-red-500">*</span></label>
                <textarea name="body" id="body" rows="6" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#094174] focus:border-[#094174] transition resize-y"
                    placeholder="Share your story, route details, and tips for this journey. Markdown is supported.">{{ old('body') }}</textarea>
                <p class="text-xs text-gray-500 mt-2">You can use Markdown for formatting.</p>
            </div>

            <!-- Images -->
            <div>
                <label for="images" class="block text-sm font-bold text-gray-700 mb-2">Photos</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#E2E8F0] file:text-[#094174] hover:file:bg-[#CBD5E1] transition">
                <p class="text-xs text-gray-500 mt-2">Select up to 12 images. Maximum 2MB per image to ensure successful upload.</p>
                <p id="image-error" class="text-sm text-red-600 mt-2 hidden"></p>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end">
                <button type="submit" id="submit-btn" class="bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-3 px-8 rounded-full transition shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Post Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('images').addEventListener('change', function(e) {
        const files = e.target.files;
        const errorEl = document.getElementById('image-error');
        const submitBtn = document.getElementById('submit-btn');
        let errorMsg = '';
        let totalSize = 0;

        if (files.length > 12) {
            errorMsg = 'You can only upload a maximum of 12 images.';
        } else {
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
                if (files[i].size > 2 * 1024 * 1024) { // 2MB
                    errorMsg = 'Each image must be smaller than 2MB.';
                    break;
                }
            }
            if (!errorMsg && totalSize > 24 * 1024 * 1024) { 
                errorMsg = 'Total size of all images cannot exceed 24MB.';
            }
        }

        if (errorMsg) {
            errorEl.textContent = errorMsg;
            errorEl.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorEl.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn.disabled) {
            e.preventDefault();
        }
    });
</script>
@endsection
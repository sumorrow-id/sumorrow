{{-- Shared mountain form fields. Expects $provinces and optionally $mountain. --}}
@php($mountain = $mountain ?? null)

<div class="space-y-10" x-data="{ status: '{{ old('is_active', ($mountain?->is_active ?? true) ? '1' : '0') }}' }">

    <!-- Identity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <h4 class="text-sm font-bold text-deep-midnight">{{ __('admin.section_identity') }}</h4>
            <p class="text-xs text-lithic-blue mt-1">{{ __('admin.section_identity_hint') }}</p>
        </div>
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_mountain_name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $mountain?->name) }}" required placeholder="{{ __('admin.field_mountain_name_placeholder') }}"
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('name') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('name')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="province_id" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_province') }}</label>
                <select id="province_id" name="province_id" required
                        class="w-full border rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('province_id') ? 'border-red-400' : 'border-morning-mist' }}">
                    <option value="" disabled @selected(!old('province_id', $mountain?->province_id))>{{ __('admin.select_province') }}</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->id }}" @selected((int) old('province_id', $mountain?->province_id) === $province->id)>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
                @error('province_id')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="coordinates" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_coordinates') }}</label>
                <input type="text" id="coordinates" name="coordinates" value="{{ old('coordinates', $mountain?->coordinates) }}" placeholder="7.45S 110.44E" required
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('coordinates') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('coordinates')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_description') }}</label>
                <textarea id="description" name="description" rows="4" required placeholder="{{ __('admin.field_description_placeholder') }}"
                          class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('description') ? 'border-red-400' : 'border-morning-mist' }}">{{ old('description', $mountain?->description) }}</textarea>
                @error('description')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <hr class="border-morning-mist/60">

    <!-- Trail metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <h4 class="text-sm font-bold text-deep-midnight">{{ __('admin.section_trail_metrics') }}</h4>
            <p class="text-xs text-lithic-blue mt-1">{{ __('admin.section_trail_metrics_hint') }}</p>
        </div>
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="elevation_masl" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_elevation_masl') }}</label>
                <input type="number" id="elevation_masl" name="elevation_masl" value="{{ old('elevation_masl', $mountain?->elevation_masl) }}" min="0" max="9000" required
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('elevation_masl') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('elevation_masl')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="elevation_gain_m" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_elevation_gain') }}</label>
                <input type="number" id="elevation_gain_m" name="elevation_gain_m" value="{{ old('elevation_gain_m', $mountain?->elevation_gain_m) }}" min="0" required
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('elevation_gain_m') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('elevation_gain_m')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="length_km" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_length_km') }}</label>
                <input type="number" step="0.1" id="length_km" name="length_km" value="{{ old('length_km', $mountain?->length_km) }}" min="0" required
                       class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('length_km') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('length_km')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-3">
                <label for="difficulty" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.table_difficulty') }}</label>
                <select id="difficulty" name="difficulty" required
                        class="w-full md:w-1/2 border rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('difficulty') ? 'border-red-400' : 'border-morning-mist' }}">
                    @foreach (['easy' => __('admin.difficulty_easy'), 'moderate' => __('admin.difficulty_moderate'), 'hard' => __('admin.difficulty_hard'), 'strenuous' => __('admin.difficulty_strenuous')] as $level => $label)
                        <option value="{{ $level }}" @selected(old('difficulty', $mountain?->difficulty) === $level)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('difficulty')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <hr class="border-morning-mist/60">

    <!-- Availability -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <h4 class="text-sm font-bold text-deep-midnight">{{ __('admin.section_availability') }}</h4>
            <p class="text-xs text-lithic-blue mt-1">{{ __('admin.section_availability_hint') }}</p>
        </div>
        <div class="lg:col-span-2 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" role="radiogroup" aria-label="{{ __('admin.route_status_label') }}">
                <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-colors"
                       :class="status === '1' ? 'border-glacial-teal bg-glacial-teal/5' : 'border-morning-mist hover:border-glacial-teal/50'">
                    <input type="radio" name="is_active" value="1" x-model="status" class="mt-0.5 text-glacial-teal focus:ring-glacial-teal">
                    <span>
                        <span class="block text-sm font-bold text-deep-midnight">{{ __('admin.status_open') }}</span>
                        <span class="block text-xs text-lithic-blue mt-0.5">{{ __('admin.status_open_hint') }}</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-colors"
                       :class="status === '0' ? 'border-blue-bird bg-morning-mist/15' : 'border-morning-mist hover:border-blue-bird/50'">
                    <input type="radio" name="is_active" value="0" x-model="status" class="mt-0.5 text-blue-bird focus:ring-blue-bird">
                    <span>
                        <span class="block text-sm font-bold text-deep-midnight">{{ __('admin.status_closed') }}</span>
                        <span class="block text-xs text-lithic-blue mt-0.5">{{ __('admin.status_closed_hint') }}</span>
                    </span>
                </label>
            </div>

            <div x-show="status === '0'" x-cloak>
                <label for="closed_since" class="block text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.field_closed_since') }}</label>
                <input type="date" id="closed_since" name="closed_since" value="{{ old('closed_since', $mountain?->closed_since?->format('Y-m-d')) }}"
                       class="w-full sm:w-64 border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-summit-blue {{ $errors->has('closed_since') ? 'border-red-400' : 'border-morning-mist' }}">
                @error('closed_since')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 pt-2 border-t border-morning-mist/60">
        <button type="submit" class="mt-4 text-sm font-semibold bg-summit-blue text-white px-6 py-3 rounded-xl hover:bg-deep-midnight transition-colors shadow-sm">
            {{ $mountain ? __('common.save_changes') : __('admin.create_mountain') }}
        </button>
        <a href="{{ route('admin.mountain-data') }}" class="mt-4 text-sm font-semibold text-lithic-blue px-6 py-3 rounded-xl hover:bg-morning-mist/30 transition-colors">
            {{ __('common.cancel') }}
        </a>
    </div>
</div>

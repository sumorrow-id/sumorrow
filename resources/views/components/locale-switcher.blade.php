@props([
    'buttonClass' => 'text-gray-400 hover:bg-[#094174]/10 hover:text-[#094174]',
    'activeClass' => 'bg-[#094174]/10 text-[#094174]',
    'panelClass' => 'bg-white border-gray-200/80',
    'inline' => false,
])

@php
    $locales = ['en', 'id'];
    $current = app()->getLocale();
@endphp

@if ($inline)
    <div data-locale-switcher aria-label="{{ __('common.language_switcher') }}" {{ $attributes->class(['grid grid-cols-2 gap-2 w-full']) }}>
        @foreach ($locales as $code)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                aria-label="{{ __('common.switch_to_' . ($code === 'en' ? 'english' : 'indonesian')) }}"
                @if (app()->isLocale($code)) aria-current="page" @endif
                @class([
                    'flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-bold transition-colors',
                    $activeClass . ' border-transparent' => app()->isLocale($code),
                    $buttonClass . ' border-gray-200' => ! app()->isLocale($code),
                ])>
                <x-flag-icon :code="$code" size="w-6 h-4" />
                <span class="uppercase">{{ $code }}</span>
            </a>
        @endforeach
    </div>
@else
<div data-locale-switcher aria-label="{{ __('common.language_switcher') }}" {{ $attributes->class(['relative group inline-block text-left']) }}>
    <button type="button"
        class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-bold transition-colors {{ app()->isLocale($current) ? $activeClass : $buttonClass }}"
        aria-label="{{ __('common.language_switcher') }}">
        <x-flag-icon :code="$current" />
        <span class="uppercase">{{ $current }}</span>
        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    <div class="absolute right-0 mt-2 w-36 rounded-xl border shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right z-50 overflow-hidden {{ $panelClass }}">
        @foreach ($locales as $code)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                aria-label="{{ __('common.switch_to_' . ($code === 'en' ? 'english' : 'indonesian')) }}"
                @if (app()->isLocale($code)) aria-current="page" @endif
                @class([
                    'flex items-center gap-2 px-3 py-2 text-xs font-bold transition-colors',
                    $activeClass => app()->isLocale($code),
                    $buttonClass => ! app()->isLocale($code),
                ])>
                <x-flag-icon :code="$code" />
                <span class="uppercase">{{ $code }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif

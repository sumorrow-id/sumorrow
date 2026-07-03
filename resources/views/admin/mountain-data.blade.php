@extends('layouts.admin')

@section('title', __('admin.mountain_data_title') . ' - Sumorrow Admin')
@section('page_title', __('admin.nav_mountain_data'))

@section('admin_content')
@php
    $difficultyMeta = [
        'easy' => ['label' => __('admin.difficulty_easy'), 'level' => 1, 'classes' => 'text-glacial-teal', 'bar' => 'bg-glacial-teal'],
        'moderate' => ['label' => __('admin.difficulty_moderate'), 'level' => 2, 'classes' => 'text-blue-bird', 'bar' => 'bg-blue-bird'],
        'hard' => ['label' => __('admin.difficulty_hard'), 'level' => 3, 'classes' => 'text-amber-600', 'bar' => 'bg-amber-500'],
        'strenuous' => ['label' => __('admin.difficulty_strenuous'), 'level' => 4, 'classes' => 'text-red-600', 'bar' => 'bg-red-500'],
    ];
@endphp

<header class="mb-10 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-[11px] font-bold text-glacial-teal uppercase tracking-[0.25em]">{{ __('admin.nav_management') }}</p>
        <h1 class="text-4xl font-extrabold text-heading mt-2">{{ __('admin.mountain_registry') }}</h1>
        <p class="text-sm text-lithic-blue mt-2">{{ __('admin.mountain_registry_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.mountains.create') }}" class="flex items-center gap-2 text-sm font-semibold bg-summit-blue text-white px-5 py-3 rounded-xl hover:bg-deep-midnight transition-colors shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        {{ __('admin.add_mountain') }}
    </a>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.total_mountains') }}</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($totalMountains) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.active_routes') }}</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($activeMountains) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">{{ __('admin.hard_strenuous') }}</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($challengingRoutes) }}</h3>
    </div>
</div>

<section class="bg-white rounded-3xl border border-morning-mist/70 shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-morning-mist/60 flex flex-wrap justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-deep-midnight">{{ __('admin.all_mountains') }}</h3>
        <form method="GET" action="{{ route('admin.mountain-data') }}" role="search" class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-lithic-blue pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.search_mountain_placeholder') }}"
                   class="w-64 pl-10 pr-4 py-2.5 text-sm border border-morning-mist rounded-xl focus:outline-none focus:ring-2 focus:ring-summit-blue focus:border-summit-blue placeholder:text-lithic-blue/60">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-morning-mist/20">
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_mountain') }}</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_elevation') }}</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_difficulty') }}</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_status') }}</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">{{ __('admin.table_action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-morning-mist/50">
                @forelse ($mountains as $mountain)
                    @php($meta = $difficultyMeta[$mountain->difficulty] ?? $difficultyMeta['moderate'])
                    <tr class="hover:bg-morning-mist/10 transition-colors">
                        <td class="px-8 py-4">
                            <p class="text-sm font-bold text-deep-midnight">{{ $mountain->name }}</p>
                            <p class="text-xs text-lithic-blue">{{ $mountain->province?->name ?? __('admin.unknown_province') }}</p>
                        </td>
                        <td class="px-8 py-4 text-sm text-lithic-blue tabular-nums whitespace-nowrap">{{ number_format($mountain->elevation_masl) }} {{ __('admin.unit_masl') }}</td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-2.5" title="{{ __('admin.difficulty_label', ['label' => $meta['label']]) }}">
                                <span class="flex items-end gap-0.5" aria-hidden="true">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <span class="w-1 rounded-full {{ $i <= $meta['level'] ? $meta['bar'] : 'bg-morning-mist' }}" style="height: {{ 5 + $i * 3 }}px"></span>
                                    @endfor
                                </span>
                                <span class="text-xs font-bold {{ $meta['classes'] }}">{{ $meta['label'] }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            @if($mountain->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold bg-glacial-teal/15 text-glacial-teal rounded-full uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-glacial-teal"></span> {{ __('admin.status_open') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold bg-morning-mist/40 text-blue-bird rounded-full uppercase tracking-wider" @if($mountain->closed_since) title="{{ __('admin.closed_since', ['date' => $mountain->closed_since->translatedFormat('j F Y')]) }}" @endif>
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-bird"></span> {{ __('admin.status_closed') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.mountains.edit', $mountain) }}" class="inline-block text-sm font-semibold text-summit-blue border border-morning-mist px-4 py-2 rounded-lg hover:bg-summit-blue hover:text-white hover:border-summit-blue transition-colors">{{ __('common.edit') }}</a>
                            <form method="POST" action="{{ route('admin.mountains.destroy', $mountain) }}" class="inline ml-2" onsubmit="return confirm({{ Illuminate\Support\Js::from(__('admin.confirm_delete_mountain', ['name' => $mountain->name])) }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 border border-red-200 px-4 py-2 rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-14 text-center">
                            @if ($search !== '')
                                <p class="text-sm font-bold text-deep-midnight">{!! __('admin.no_mountains_match', ['search' => e($search)]) !!}</p>
                                <a href="{{ route('admin.mountain-data') }}" class="mt-2 inline-block text-sm font-semibold text-summit-blue hover:underline">{{ __('admin.clear_search') }}</a>
                            @else
                                <p class="text-sm font-bold text-deep-midnight">{{ __('admin.registry_empty') }}</p>
                                <p class="text-xs text-lithic-blue mt-1">{{ __('admin.registry_empty_hint') }}</p>
                                <a href="{{ route('admin.mountains.create') }}" class="mt-4 inline-block text-sm font-semibold bg-summit-blue text-white px-5 py-2.5 rounded-xl hover:bg-deep-midnight transition-colors">{{ __('admin.add_mountain') }}</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($mountains->hasPages())
        <div class="px-8 py-5 border-t border-morning-mist/60">
            {{ $mountains->links() }}
        </div>
    @endif
</section>
@endsection

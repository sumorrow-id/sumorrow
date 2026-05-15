@extends('layouts.admin')

@section('title', 'Mountain Data - Sumorrow Admin')
@section('page_title', 'Mountain Data')

@section('admin_content')
<header class="mb-12">
    <h1 class="text-4xl font-extrabold text-heading mt-2">Mountain Registry</h1>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Total Mountains</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($totalMountains) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Active Routes</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($activeMountains) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Hard/Strenuous</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($challengingRoutes) }}</h3>
    </div>
</div>

<section class="bg-white rounded-3xl border border-morning-mist shadow-sm overflow-hidden">
    <div class="p-8 border-b border-morning-mist flex justify-between items-center">
        <h3 class="text-xl font-bold text-deep-midnight">Latest Mountain Data</h3>
        <button class="text-sm font-semibold text-summit-blue hover:underline">Manage Data</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-morning-mist/30">
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Mountain</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Elevation</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Difficulty</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-morning-mist">
                @forelse ($mountains as $mountain)
                    <tr>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-deep-midnight">{{ $mountain->name }}</p>
                            <p class="text-xs text-lithic-blue">{{ $mountain->province?->name ?? 'Unknown Province' }}</p>
                        </td>
                        <td class="px-8 py-6 text-sm text-lithic-blue">{{ number_format($mountain->elevation_masl) }} masl</td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-bold bg-sky-oxygen/20 text-summit-blue rounded-full uppercase">
                                {{ $mountain->difficulty }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            @if($mountain->is_active)
                                <span class="px-3 py-1 text-[10px] font-bold bg-glacial-teal/20 text-glacial-teal rounded-full uppercase">Open</span>
                            @else
                                <span class="px-3 py-1 text-[10px] font-bold bg-morning-mist text-blue-bird rounded-full uppercase">Closed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-sm text-lithic-blue">Belum ada data gunung.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@extends('layouts.admin')

@section('title', __('admin.dashboard_title') . ' - Sumorrow Admin')
@section('page_title', __('admin.nav_dashboard'))

@section('admin_content')
<header class="mb-10">
    <p class="text-[11px] font-bold text-glacial-teal uppercase tracking-[0.25em]">{{ __('admin.nav_overview') }}</p>
    <h1 class="text-4xl font-extrabold text-heading mt-2">{{ __('admin.control_center') }}</h1>
    <p class="text-sm text-lithic-blue mt-2">{{ now()->translatedFormat('l, j F Y') }} &middot; {{ __('admin.signed_in_as') }} <span class="font-bold text-deep-midnight">{{ auth()->user()->username }}</span></p>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-10 h-10 rounded-xl bg-summit-blue/10 text-summit-blue flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z" /></svg>
            </span>
            <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.total_users') }}</p>
        </div>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($totalUsers) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-10 h-10 rounded-xl bg-glacial-teal/10 text-glacial-teal flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            </span>
            <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.new_this_week') }}</p>
        </div>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($newUsersCount) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-10 h-10 rounded-xl bg-blue-bird/10 text-blue-bird flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </span>
            <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.total_mountains') }}</p>
        </div>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($totalMountains) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-10 h-10 rounded-xl bg-sky-oxygen/15 text-summit-blue flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </span>
            <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.active_routes') }}</p>
        </div>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($activeMountains) }}</h3>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Recent Users Table -->
    <section class="xl:col-span-2 bg-white rounded-3xl border border-morning-mist/70 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-morning-mist/60 flex justify-between items-center">
            <h3 class="text-lg font-bold text-deep-midnight">{{ __('admin.recently_registered_users') }}</h3>
            <a href="{{ route('admin.user-updates') }}" class="text-sm font-semibold text-summit-blue hover:underline">{{ __('admin.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-morning-mist/20">
                        <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_user') }}</th>
                        <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">{{ __('admin.table_role') }}</th>
                        <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">{{ __('admin.table_joined') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-morning-mist/50">
                    @forelse($recentUsers as $user)
                    <tr class="hover:bg-morning-mist/10 transition-colors">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ __('common.avatar_alt') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'" class="w-9 h-9 rounded-full border border-morning-mist">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-summit-blue flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-deep-midnight truncate">{{ $user->username }}</p>
                                    <p class="text-xs text-lithic-blue truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            @if ($user->role === 'admin')
                                <span class="px-3 py-1 text-[10px] font-bold bg-summit-blue text-white rounded-full uppercase tracking-wider">{{ __('admin.role_admin') }}</span>
                            @else
                                <span class="px-3 py-1 text-[10px] font-bold bg-morning-mist/40 text-blue-bird rounded-full uppercase tracking-wider">{{ __('admin.role_user') }}</span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-right">
                            <span class="text-xs text-lithic-blue font-semibold whitespace-nowrap">{{ $user->created_at?->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-12 text-center">
                            <p class="text-sm font-semibold text-lithic-blue">{{ __('admin.no_users_registered_yet') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="bg-white rounded-3xl border border-morning-mist/70 shadow-sm p-8 h-fit">
        <h3 class="text-lg font-bold text-deep-midnight mb-5">{{ __('admin.quick_actions') }}</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.mountains.create') }}" class="flex items-center gap-4 p-4 rounded-2xl border border-morning-mist/70 hover:border-summit-blue/40 hover:bg-morning-mist/10 transition-colors group">
                <span class="w-10 h-10 rounded-xl bg-summit-blue text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-deep-midnight group-hover:text-summit-blue">{{ __('admin.action_add_mountain') }}</p>
                    <p class="text-xs text-lithic-blue">{{ __('admin.action_add_mountain_hint') }}</p>
                </div>
            </a>
            <a href="{{ route('admin.user-updates') }}" class="flex items-center gap-4 p-4 rounded-2xl border border-morning-mist/70 hover:border-summit-blue/40 hover:bg-morning-mist/10 transition-colors group">
                <span class="w-10 h-10 rounded-xl bg-glacial-teal text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-deep-midnight group-hover:text-summit-blue">{{ __('admin.action_manage_users') }}</p>
                    <p class="text-xs text-lithic-blue">{{ __('admin.action_manage_users_hint') }}</p>
                </div>
            </a>
            <a href="{{ route('admin.users.export') }}" class="flex items-center gap-4 p-4 rounded-2xl border border-morning-mist/70 hover:border-summit-blue/40 hover:bg-morning-mist/10 transition-colors group">
                <span class="w-10 h-10 rounded-xl bg-blue-bird text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-deep-midnight group-hover:text-summit-blue">{{ __('admin.action_export_users') }}</p>
                    <p class="text-xs text-lithic-blue">{{ __('admin.action_export_users_hint') }}</p>
                </div>
            </a>
        </div>
    </section>
</div>
@endsection

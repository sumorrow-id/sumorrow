@extends('layouts.admin')

@section('title', 'User Updates - Sumorrow Admin')
@section('page_title', 'User Updates')

@section('admin_content')
<header class="mb-10">
    <p class="text-[11px] font-bold text-glacial-teal uppercase tracking-[0.25em]">Management</p>
    <h1 class="text-4xl font-extrabold text-heading mt-2">Community Growth</h1>
    <p class="text-sm text-lithic-blue mt-2">Review new sign-ups, adjust roles, or remove accounts.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">Total Users</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($totalUsers) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">New This Week</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($newThisWeek) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-morning-mist/70 shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-2">New This Month</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight tabular-nums">{{ number_format($newThisMonth) }}</h3>
    </div>
</div>

<section class="bg-white rounded-3xl border border-morning-mist/70 shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-morning-mist/60 flex flex-wrap justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-deep-midnight">Registered Users</h3>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.user-updates') }}" role="search" class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-lithic-blue pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name or email"
                       class="w-56 pl-10 pr-4 py-2.5 text-sm border border-morning-mist rounded-xl focus:outline-none focus:ring-2 focus:ring-summit-blue focus:border-summit-blue placeholder:text-lithic-blue/60">
            </form>
            <a href="{{ route('admin.users.export') }}" class="flex items-center gap-2 text-sm font-semibold text-summit-blue border border-morning-mist px-4 py-2.5 rounded-xl hover:bg-morning-mist/20 transition-colors whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-morning-mist/20">
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">User</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Joined</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Role</th>
                    <th class="px-8 py-3.5 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-morning-mist/50">
                @forelse ($recentUsers as $user)
                    <tr class="hover:bg-morning-mist/10 transition-colors">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="avatar" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'" class="w-9 h-9 rounded-full border border-morning-mist">
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
                            <span class="text-xs text-lithic-blue font-semibold whitespace-nowrap" title="{{ $user->created_at?->format('j F Y H:i') }}">
                                {{ $user->created_at?->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-8 py-4">
                            @if ($user->id === auth()->id())
                                <span class="px-3 py-1 text-[10px] font-bold bg-summit-blue text-white rounded-full uppercase tracking-wider">{{ $user->role }} · you</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="role-{{ $user->id }}">Role for {{ $user->username }}</label>
                                    <select id="role-{{ $user->id }}" name="role" onchange="this.form.submit()"
                                            class="text-xs font-bold text-summit-blue border border-morning-mist rounded-lg px-2.5 py-1.5 bg-white cursor-pointer focus:outline-none focus:ring-2 focus:ring-summit-blue">
                                        <option value="user" @selected($user->role === 'user')>User</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-right">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete user {{ $user->username }}? Their posts, ratings, and gear will also be removed. This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-600 border border-red-200 px-4 py-2 rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-14 text-center">
                            @if ($search !== '')
                                <p class="text-sm font-bold text-deep-midnight">No users match &ldquo;{{ $search }}&rdquo;</p>
                                <a href="{{ route('admin.user-updates') }}" class="mt-2 inline-block text-sm font-semibold text-summit-blue hover:underline">Clear search</a>
                            @else
                                <p class="text-sm font-semibold text-lithic-blue">No users registered yet.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($recentUsers->hasPages())
        <div class="px-8 py-5 border-t border-morning-mist/60">
            {{ $recentUsers->links() }}
        </div>
    @endif
</section>
@endsection

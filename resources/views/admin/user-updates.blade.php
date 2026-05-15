@extends('layouts.admin')

@section('title', 'User Updates - Sumorrow Admin')
@section('page_title', 'User Updates')

@section('admin_content')
<header class="mb-12">
    <h1 class="text-4xl font-extrabold text-heading mt-2">Community Growth</h1>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Total Users</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($totalUsers) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">New This Week</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($newThisWeek) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">New This Month</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($newThisMonth) }}</h3>
    </div>
</div>

<section class="bg-white rounded-3xl border border-morning-mist shadow-sm overflow-hidden">
    <div class="p-8 border-b border-morning-mist flex justify-between items-center">
        <h3 class="text-xl font-bold text-deep-midnight">Recent Registered Users</h3>
        <button class="text-sm font-semibold text-summit-blue hover:underline">Export CSV</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-morning-mist/30">
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Name</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Email</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Joined</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-morning-mist">
                @forelse ($recentUsers as $user)
                    <tr>
                        <td class="px-8 py-6 text-sm font-bold text-deep-midnight">{{ $user->username }}</td>
                        <td class="px-8 py-6 text-sm text-lithic-blue">{{ $user->email }}</td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-bold bg-sky-oxygen/20 text-summit-blue rounded-full uppercase">
                                {{ $user->created_at?->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <button class="text-sm font-semibold bg-summit-blue text-white px-4 py-2 rounded-lg hover:bg-deep-midnight transition">
                                View Profile
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-sm text-lithic-blue">Belum ada user terbaru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

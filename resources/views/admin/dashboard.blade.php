@extends('layouts.admin')

@section('title', 'Dashboard - Sumorrow Admin')
@section('page_title', 'Dashboard')

@section('admin_content')
<header class="mb-12">
    <h1 class="text-4xl font-extrabold text-heading mt-2">Control Center</h1>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Pending Reports</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">0</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">New Users (This Week)</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($newUsersCount) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Total Forum Posts</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($forumPostsCount) }}</h3>
    </div>
</div>

<!-- Moderation Table -->
<section class="bg-white rounded-3xl border border-morning-mist shadow-sm overflow-hidden">
    <div class="p-8 border-b border-morning-mist flex justify-between items-center">
        <h3 class="text-xl font-bold text-deep-midnight">Recent Forum Activity</h3>
        <a href="{{ route('admin.forum-moderation') }}" class="text-sm font-semibold text-summit-blue hover:underline">View All</a>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="bg-morning-mist/30">
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">User</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Content</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Date</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-morning-mist">
            @forelse($recentPosts as $post)
            <tr>
                <td class="px-8 py-6">
                    <div class="flex items-center gap-3">
                        @if($post->user->avatar_url)
                            <img src="{{ $post->user->avatar_url }}" alt="avatar" class="w-8 h-8 rounded-full border border-morning-mist">
                        @else
                            <div class="w-8 h-8 rounded-full bg-summit-blue flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($post->user->username, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-sm font-bold text-deep-midnight">{{ $post->user->username }}</span>
                    </div>
                </td>
                <td class="px-8 py-6">
                    <p class="text-sm text-lithic-blue line-clamp-1">"{{ Str::limit($post->title, 50) }}"</p>
                </td>
                <td class="px-8 py-6">
                    <span class="text-xs text-lithic-blue font-semibold">{{ $post->created_at->diffForHumans() }}</span>
                </td>
                <td class="px-8 py-6 text-right">
                    <a href="{{ route('admin.forum-moderation') }}" class="text-sm font-semibold bg-summit-blue text-white px-4 py-2 rounded-lg hover:bg-deep-midnight transition">Moderate</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-8 py-6 text-center text-sm text-lithic-blue font-semibold">No recent activity.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
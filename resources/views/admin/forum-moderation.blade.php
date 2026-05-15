@extends('layouts.admin')

@section('title', 'Forum Moderation - Sumorrow Admin')
@section('page_title', 'Forum Moderation')

@section('admin_content')
<header class="mb-12">
    <h1 class="text-4xl font-extrabold text-heading mt-2">Moderation Queue</h1>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Total Posts</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($totalPosts) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Posts Today</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($postsToday) }}</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Total Replies</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">{{ number_format($totalReplies) }}</h3>
    </div>
</div>

<section class="bg-white rounded-3xl border border-morning-mist shadow-sm overflow-hidden">
    <div class="p-8 border-b border-morning-mist flex justify-between items-center">
        <h3 class="text-xl font-bold text-deep-midnight">Recent Forum Posts</h3>
        <button class="text-sm font-semibold text-summit-blue hover:underline">Review Rules</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-morning-mist/30">
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Author</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Post</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Created</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-morning-mist">
                @forelse ($recentPosts as $post)
                    <tr>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-deep-midnight">{{ $post->author->name ?? 'Unknown User' }}</p>
                            <p class="text-xs text-lithic-blue">{{ $post->author->email ?? '-' }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-deep-midnight">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</p>
                            <p class="text-sm text-lithic-blue line-clamp-1">{{ \Illuminate\Support\Str::limit($post->body, 90) }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-bold bg-sky-oxygen/20 text-summit-blue rounded-full uppercase">
                                {{ $post->created_at?->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <button class="text-sm font-semibold bg-summit-blue text-white px-4 py-2 rounded-lg hover:bg-deep-midnight transition">
                                Moderate
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-sm text-lithic-blue">Belum ada post untuk dimoderasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

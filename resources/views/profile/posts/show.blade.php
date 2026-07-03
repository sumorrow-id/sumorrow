@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-4xl mx-auto pt-32 pb-16 px-4 sm:px-8">
    <div class="mb-8">
        <a href="{{ url()->previous() == route('profile.posts.index') ? route('profile.posts.index') : route('profile') }}" class="text-sm font-bold text-[#094174] hover:underline flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">
        <div class="mb-8 border-b pb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] break-words">
                    {{ $post->title }}
                </h1>
                
                @if ($post->duration_days)
                    <span class="bg-[#BDE0FE] text-[#1E40AF] text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap self-start md:self-auto">
                        {{ $post->duration_days }}D Expedition
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                @if ($post->mountain)
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('explore.show', $post->mountain->id) }}" class="font-semibold text-[#094174] hover:underline">
                            {{ $post->mountain->name }}
                        </a>
                        <span>• {{ $post->mountain->province?->name ?? 'Location varies' }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-1.5 border-l pl-4 border-gray-200">
                    <span>{{ $post->climbing_date ? $post->climbing_date->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <img src="{{ $post->user->avatar_url ? (str_contains($post->user->avatar_url, 'http') ? $post->user->avatar_url : asset('storage/' . $post->user->avatar_url)) : 'https://ui-avatars.com/api/?name=' . urlencode(substr($post->user->username, 0, 2)) }}" 
                     alt="{{ $post->user->username }}" 
                     class="w-10 h-10 rounded-full object-cover bg-gray-200">
                <div>
                    <p class="font-bold text-[#0F172A] text-sm">{{ $post->user->username }}</p>
                    <p class="text-xs text-gray-500">Author</p>
                </div>
            </div>
        </div>

        <div class="prose max-w-none text-gray-700 leading-relaxed mb-10">
            {!! Str::markdown($post->body) !!}
        </div>

        @if ($post->images->count() > 0)
            <div class="space-y-4">
                <h3 class="font-bold text-lg text-[#0F172A]">Photos ({{ $post->images->count() }})</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($post->images as $image)
                        <a href="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" target="_blank" class="block aspect-square overflow-hidden rounded-2xl bg-gray-100 group">
                            <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" 
                                 alt="Post photo" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
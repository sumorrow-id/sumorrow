@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-4xl mx-auto pt-32 pb-16 px-4 sm:px-8">
    <div class="mb-8 flex items-center justify-between gap-4">
        <a href="{{ url()->previous() == route('profile.posts.index') ? route('profile.posts.index') : route('profile') }}" class="text-sm font-bold text-[#094174] hover:underline flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('profile.back') }}
        </a>

        <form method="POST" action="{{ route('community.posts.destroy', $post->id) }}"
            onsubmit="return confirm({{ Illuminate\Support\Js::from(__('community.confirm_delete_post')) }})">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to_activities" value="1">
            <button type="submit"
                class="text-center border border-red-200 text-red-500 hover:bg-red-50 text-sm font-bold py-2 px-5 rounded-full transition">
                {{ __('common.delete') }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">
        <div class="mb-8 border-b pb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] break-words">
                    {{ $post->title }}
                </h1>
                
                @if ($post->duration_days)
                    <span class="bg-[#BDE0FE] text-[#1E40AF] text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap self-start md:self-auto">
                        {{ __('profile.expedition_days_badge', ['days' => $post->duration_days]) }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                @if ($post->mountain)
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('explore.show', $post->mountain->id) }}" class="font-semibold text-[#094174] hover:underline">
                            {{ $post->mountain->name }}
                        </a>
                        <span>• {{ $post->mountain->province?->name ?? __('profile.location_varies') }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-1.5 border-l pl-4 border-gray-200">
                    <span>{{ $post->climbing_date ? $post->climbing_date->translatedFormat('M d, Y') : $post->created_at->translatedFormat('M d, Y') }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <img src="{{ $post->author->avatar_url ? (str_contains($post->author->avatar_url, 'http') ? $post->author->avatar_url : asset('storage/' . $post->author->avatar_url)) : 'https://ui-avatars.com/api/?name=' . urlencode(substr($post->author->username, 0, 2)) }}" 
                     alt="{{ $post->author->username }}" 
                     class="w-10 h-10 rounded-full object-cover bg-gray-200">
                <div>
                    <p class="font-bold text-[#0F172A] text-sm">{{ $post->author->username }}</p>
                    <p class="text-xs text-gray-500">{{ __('profile.author') }}</p>
                </div>
            </div>
        </div>

        <div class="prose max-w-none text-gray-700 leading-relaxed mb-10">
            {!! Str::markdown($post->body) !!}
        </div>

        @if ($post->images->count() > 0)
            <div class="space-y-4">
                <h3 class="font-bold text-lg text-[#0F172A]">{{ __('profile.photos_count', ['count' => $post->images->count()]) }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($post->images as $image)
                        <a href="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" target="_blank" class="block aspect-square overflow-hidden rounded-2xl bg-gray-100 group">
                            <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}"
                                 alt="{{ __('profile.post_photo_alt') }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-350 mx-auto pt-32 pb-8 px-4 sm:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] tracking-wide mb-2">My Activities</h1>
            <p class="text-[#6D8A9F] text-sm md:text-base">All your hiking stories and upcoming expeditions</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('profile') }}" class="text-sm font-bold text-[#094174] hover:underline self-center">Back to Profile</a>
            <a href="{{ route('profile.posts.create') }}" class="bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2 px-6 rounded-full transition shadow-md hover:shadow-lg whitespace-nowrap">
                + New Activity
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($posts as $post)
            <a href="{{ route('profile.posts.show', $post->id) }}" class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex flex-col h-full hover:-translate-y-1 transition duration-300">
                <!-- Images Carousel if applicable, keeping simple for lists -->
                @if($post->images && $post->images->count() > 0)
                    <div class="w-full h-48 rounded-2xl overflow-hidden mb-4 bg-gray-100">
                        <img src="{{ str_contains($post->images->first()->image_url, 'http') ? $post->images->first()->image_url : asset('storage/' . $post->images->first()->image_url) }}" alt="Post Image" class="w-full h-full object-cover">
                    </div>
                @endif
                
                <div class="flex-grow">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="text-lg font-bold text-[#0F172A] line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        @if ($post->duration_days)
                            <span class="bg-[#BDE0FE] text-[#1E40AF] text-[10px] font-semibold px-2 py-1 rounded-full whitespace-nowrap mt-1">
                                {{ $post->duration_days }}D
                            </span>
                        @endif
                    </div>
                    
                    @if ($post->mountain)
                        <p class="text-[12px] text-[#094174] font-semibold mb-1">{{ $post->mountain->name }}</p>
                    @endif
                    
                    <p class="text-[12px] text-gray-500 mb-3">
                        {{ $post->mountain?->province?->name ?? 'Location varies' }} • 
                        {{ $post->climbing_date ? $post->climbing_date->format('M d, Y') : $post->created_at->format('M d, Y') }}
                    </p>

                    <div class="text-sm text-gray-600 line-clamp-3">
                        {!! Str::markdown($post->body) !!}
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-3xl shadow-sm border border-gray-100">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No activities yet</h3>
                <p class="text-gray-500 mb-6">Start sharing your hiking adventures and experiences.</p>
                <a href="{{ route('profile.posts.create') }}" class="inline-block bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-2 px-6 rounded-full transition shadow-md">
                    Create First Activity
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
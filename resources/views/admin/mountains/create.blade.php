@extends('layouts.admin')

@section('title', __('admin.add_mountain_title') . ' - Sumorrow Admin')
@section('page_title', __('admin.add_mountain'))

@section('admin_content')
<header class="mb-10">
    <a href="{{ route('admin.mountain-data') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-summit-blue hover:underline">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        {{ __('admin.mountain_registry') }}
    </a>
    <h1 class="text-4xl font-extrabold text-heading mt-3">{{ __('admin.add_new_mountain') }}</h1>
    <p class="text-sm text-lithic-blue mt-2">{{ __('admin.add_new_mountain_subtitle') }}</p>
</header>

<section class="bg-white rounded-3xl border border-morning-mist/70 shadow-sm p-8">
    <form method="POST" action="{{ route('admin.mountains.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.mountains.form')
    </form>
</section>
@endsection

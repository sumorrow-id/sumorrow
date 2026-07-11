@extends('layouts.app')

@php
    $code = $exception->getStatusCode();
    $key = Lang::has("errors.$code") ? $code : 'default';
@endphp

@section('content')
    <section class="flex flex-col items-center justify-center text-center px-6 py-28">
        <p class="text-7xl font-extrabold text-[#094174]">{{ $code }}</p>
        <h1 class="mt-4 text-2xl font-bold text-[#001E3A]">{{ __("errors.$key.title") }}</h1>
        <p class="mt-2 max-w-md text-sm text-gray-500 leading-relaxed">{{ __("errors.$key.message") }}</p>
        <a
            href="{{ route('home') }}"
            class="mt-8 text-sm font-bold bg-[#094174] hover:bg-[#105DA3] text-white px-6 py-2.5 rounded-full transition shadow-sm"
        >{{ __('errors.back_home') }}</a>
    </section>
@endsection

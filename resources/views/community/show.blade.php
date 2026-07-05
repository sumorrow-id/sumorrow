@extends('layouts.app')

@section('content')
@php
    $isCreator = $community->isCreatedBy(Auth::user());
    $isMember = Auth::check() && $community->members->contains('id', Auth::id());
    $editHasErrors = $errors->hasAny(['name', 'description', 'privacy', 'profile_image', 'banner_image']);
    $eventHasErrors = $errors->getBag('event')->any();
@endphp

<div class="w-[95%] sm:w-[90%] max-w-6xl mx-auto pt-24 md:pt-32 pb-12">

    {{-- Back link --}}
    <a href="{{ route('community', ['tab' => 'community']) }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#094174] transition mb-4 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        {{ __('community.back_to_community') }}
    </a>

    {{-- ================= Header card ================= --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Banner --}}
        <div class="h-40 sm:h-52 md:h-64 w-full bg-gray-200">
            <img src="{{ $community->bannerImageUrl() }}" alt="{{ __('community.banner_image_label') }}"
                class="w-full h-full object-cover">
        </div>

        {{-- Profile + meta --}}
        <div class="px-4 sm:px-6 pb-5 relative pt-14 sm:pt-4 flex flex-col sm:flex-row sm:items-end justify-between gap-4">

            <div class="absolute -top-12 sm:-top-14 left-4 sm:left-6 w-20 h-20 sm:w-28 sm:h-28 rounded-2xl border-4 border-white bg-gray-100 shadow-md overflow-hidden">
                <img src="{{ $community->profileImageUrl() }}" alt="{{ __('community.profile_image_label') }}"
                    class="w-full h-full object-cover">
            </div>

            <div class="sm:pl-32">
                <span class="text-xs text-gray-400 font-medium">
                    {{ __('community.created_label', ['date' => $community->created_at?->translatedFormat('F Y')]) }}
                </span>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-[#094174] tracking-tight mt-0.5 break-words">
                    {{ $community->name }}
                </h1>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <span class="inline-block px-2.5 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded-md {{ $community->privacy === 'public' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ __('community.privacy_'.$community->privacy) }}
                    </span>
                    <span class="text-xs text-gray-400 font-medium">
                        • {{ __('community.members_count', ['count' => $community->members->count()]) }}
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                <div class="flex -space-x-3 mr-1">
                    @foreach ($community->members->take(3) as $member)
                        <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover"
                            src="{{ $member->avatar_url ? asset($member->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                            alt="{{ $member->username }}">
                    @endforeach
                </div>

                @if ($isCreator)
                    <button type="button" onclick="toggleEditCommunityModal(true)"
                        class="px-4 py-2 bg-[#094174] text-white text-xs sm:text-sm font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                        {{ __('community.edit_community') }}
                    </button>
                    <form method="POST" action="{{ route('community.destroy', $community) }}" class="confirm-submit-form"
                        data-confirm-title="{{ __('community.delete_community') }}"
                        data-confirm-message="{{ __('community.confirm_delete_community') }}"
                        data-confirm-label="{{ __('community.confirm_delete_button') }}"
                        data-confirm-variant="danger">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-500/10 text-red-600 text-xs sm:text-sm font-bold rounded-full border border-red-200 hover:bg-red-500/20 transition cursor-pointer">
                            {{ __('community.delete_community') }}
                        </button>
                    </form>
                @endif

                @if ($isMember)
                    <form method="POST" action="{{ route('community.leave', $community) }}" class="confirm-submit-form"
                        data-confirm-title="{{ __('community.leave_community_button') }}"
                        data-confirm-message="{{ __('community.confirm_leave_community') }}"
                        data-confirm-label="{{ __('community.confirm_leave_button') }}"
                        data-confirm-variant="danger">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 {{ $isCreator ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-red-500/10 text-red-600 border border-red-200 hover:bg-red-500/20' }} text-xs sm:text-sm font-bold rounded-full transition cursor-pointer">
                            {{ __('community.leave_community_button') }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('community.join', $community) }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-[#094174] text-white text-xs sm:text-sm font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                            {{ __('community.join_community_button') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Tab navigation --}}
        <div class="border-t border-gray-100 px-4 sm:px-6">
            <div class="flex gap-6 overflow-x-auto hide-scrollbar" id="detail-tabs">
                <button type="button" onclick="switchDetailTab('forum')" id="tab-forum"
                    class="py-4 border-b-2 font-bold text-sm transition-all border-[#094174] text-[#094174] whitespace-nowrap cursor-pointer">
                    {{ __('community.tab_forum') }}
                </button>
                <button type="button" onclick="switchDetailTab('members')" id="tab-members"
                    class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap cursor-pointer">
                    {{ __('community.tab_members') }}
                </button>
                <button type="button" onclick="switchDetailTab('events')" id="tab-events"
                    class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap cursor-pointer">
                    {{ __('community.tab_events') }}
                </button>
                <button type="button" onclick="switchDetailTab('about')" id="tab-about"
                    class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap cursor-pointer">
                    {{ __('community.tab_about') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ================= Tab contents ================= --}}
    <div class="mt-6">

        {{-- Forum --}}
        <div id="content-forum" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                @include('community.components.feed', ['posts' => $posts, 'feedCommunity' => $community, 'canPostInCommunity' => $isMember])
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-[#1a2b4c] mb-4">{{ __('community.recommended_mountains') }}</h3>
                    <div class="space-y-4">
                        @foreach ($recommendedMountains as $mountain)
                            <a href="{{ route('explore.show', $mountain->id) }}" class="block group">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $mountain->images->first()?->image_url ?: asset('images/default-mountain.jpg') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default-mountain.jpg') }}'"
                                        alt="{{ $mountain->name }}" class="w-12 h-12 rounded-lg object-cover shrink-0 bg-gray-100">
                                    <div>
                                        <p class="font-semibold text-sm text-gray-700 group-hover:text-[#094174] transition">
                                            {{ $mountain->name }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $mountain->elevation_masl }} MASL</p>
                                    </div>
                                </div>
                            </a>
                            @if (!$loop->last)
                                <hr class="border-gray-100">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Members --}}
        <div id="content-members" class="hidden bg-white p-4 sm:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
            <h3 class="font-bold text-lg text-[#001E3A] mb-4">
                {{ __('community.members_heading', ['count' => $community->members->count()]) }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($community->members as $member)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($member->avatar_url)
                                <img src="{{ asset($member->avatar_url) }}" class="w-10 h-10 rounded-xl object-cover shrink-0" alt="{{ $member->username }}">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center font-bold text-[#094174] text-sm shrink-0">
                                    {{ strtoupper(substr($member->username, 0, 2)) }}
                                </div>
                            @endif
                            <h4 class="font-bold text-sm text-gray-800 truncate">
                                {{ $member->username }} {{ Auth::id() === $member->id ? __('community.you_suffix') : '' }}
                            </h4>
                        </div>
                        <span class="text-[10px] px-2.5 py-1 rounded-full font-bold shrink-0 {{ $community->created_by === $member->id ? 'bg-[#094174] text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $community->created_by === $member->id ? __('community.leader_badge') : __('community.member_badge') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 col-span-2 text-center py-4">{{ __('community.no_members_yet') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Events --}}
        <div id="content-events" class="hidden space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-[#001E3A]">{{ __('community.events_heading') }}</h3>
                @if ($isMember)
                    <button type="button" onclick="toggleEventModal(true)"
                        class="bg-[#094174] text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-[#105DA3] transition shadow-md cursor-pointer self-start sm:self-auto">
                        + {{ __('community.create_event') }}
                    </button>
                @else
                    <span class="text-xs text-gray-400 font-medium">{{ __('community.join_to_create_event') }}</span>
                @endif
            </div>

            @if ($community->events->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($community->events as $event)
                        <div class="bg-white p-5 sm:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <span class="inline-block text-xs font-bold text-[#094174] bg-blue-50 px-3 py-1 rounded-full">
                                        {{ $event->event_date->translatedFormat('d M Y') }}
                                    </span>
                                    <h4 class="font-bold text-[#001E3A] text-lg mt-3 leading-tight break-words">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1.5 font-medium flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $event->event_date->translatedFormat('H:i') }} WIB
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="break-words">{{ $event->location }}</span>
                                    </p>
                                </div>
                                @if ($event->image_url)
                                    <img src="{{ asset($event->image_url) }}" alt="{{ $event->title }}"
                                        class="w-20 h-20 rounded-2xl object-cover shrink-0">
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 mt-4 grow">{{ $event->description }}</p>

                            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-4">
                                <span class="text-xs text-gray-400 font-medium">
                                    {{ __('community.event_created_by', ['name' => $event->user->username ?? '—']) }}
                                </span>
                                @if (Auth::id() === $event->user_id || $isCreator)
                                    <form method="POST" action="{{ route('community.events.destroy', $event) }}" class="confirm-submit-form"
                                        data-confirm-title="{{ __('community.delete_event') }}"
                                        data-confirm-message="{{ __('community.confirm_delete_event') }}"
                                        data-confirm-label="{{ __('community.confirm_delete_button') }}"
                                        data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600 text-xs font-bold transition cursor-pointer">
                                            {{ __('community.delete_event') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-10 sm:p-12 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-50 text-[#094174] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-[#001E3A]">{{ __('community.no_events_title') }}</h3>
                    <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1">{{ __('community.no_events_text') }}</p>
                </div>
            @endif
        </div>

        {{-- About --}}
        <div id="content-about" class="hidden bg-white p-4 sm:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm space-y-4">
            <div>
                <h3 class="font-bold text-base text-[#001E3A] mb-1">{{ __('community.about_heading') }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $community->description ?: __('community.no_description') }}
                </p>
            </div>
            <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block mb-0.5">{{ __('community.created_by_label') }}</span>
                    <span class="font-semibold text-gray-700">{{ $community->creator->username ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-0.5">{{ __('community.privacy_label') }}</span>
                    <span class="font-semibold text-[#094174]">{{ __('community.privacy_'.$community->privacy) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= Edit modal (creator only) ================= --}}
@if ($isCreator)
    <div id="modal-edit-community" class="{{ $editHasErrors ? '' : 'hidden' }} fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl sm:text-2xl font-bold text-[#001E3A]">{{ __('community.edit_community') }}</h3>
                <button type="button" onclick="toggleEditCommunityModal(false)" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if ($editHasErrors)
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('community.update', $community) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.community_name_label') }}</label>
                    <input type="text" name="name" required value="{{ old('name', $community->name) }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.description_label') }}</label>
                    <textarea name="description" rows="3" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading resize-none">{{ old('description', $community->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.privacy_label') }}</label>
                    <select name="privacy" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading bg-white">
                        <option value="public" @selected(old('privacy', $community->privacy) === 'public')>{{ __('community.privacy_public_option') }}</option>
                        <option value="private" @selected(old('privacy', $community->privacy) === 'private')>{{ __('community.privacy_private_option') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.profile_image_label') }}</label>
                    <div class="flex items-center gap-3">
                        <img id="edit-profile-preview" src="{{ $community->profileImageUrl() }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                        <input type="file" name="profile_image" accept="image/*"
                            onchange="previewEditImage(this, 'edit-profile-preview')"
                            class="w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-1.5 file:rounded-full file:border-0 file:bg-blue-50 file:text-[#094174] file:text-xs file:font-bold file:cursor-pointer" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.banner_image_label') }}</label>
                    <div class="flex items-center gap-3">
                        <img id="edit-banner-preview" src="{{ $community->bannerImageUrl() }}" alt="" class="w-20 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                        <input type="file" name="banner_image" accept="image/*"
                            onchange="previewEditImage(this, 'edit-banner-preview')"
                            class="w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-1.5 file:rounded-full file:border-0 file:bg-blue-50 file:text-[#094174] file:text-xs file:font-bold file:cursor-pointer" />
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">{{ __('community.image_hint') }}</p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="toggleEditCommunityModal(false)"
                        class="flex-1 px-4 py-2 border border-gray-200 text-gray-500 font-bold rounded-full hover:bg-gray-50 transition cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-[#094174] text-white font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                        {{ __('community.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- ================= Create-event modal (members only) ================= --}}
@if ($isMember)
    <div id="modal-create-event" class="{{ $eventHasErrors ? '' : 'hidden' }} fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl sm:text-2xl font-bold text-[#001E3A]">{{ __('community.create_event') }}</h3>
                <button type="button" onclick="toggleEventModal(false)" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if ($eventHasErrors)
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium space-y-0.5">
                    @foreach ($errors->getBag('event')->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('community.events.store', $community) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.event_title_label') }}</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        placeholder="{{ __('community.event_title_placeholder') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.event_date_label') }}</label>
                    <input type="datetime-local" name="event_date" required value="{{ old('event_date') }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading bg-white" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.event_location_label') }}</label>
                    <input type="text" name="location" required value="{{ old('location') }}"
                        placeholder="{{ __('community.event_location_placeholder') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.description_label') }}</label>
                    <textarea name="description" rows="3" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading resize-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.event_image_label') }}</label>
                    <input type="file" name="image" accept="image/*"
                        onchange="previewEventImage(this)"
                        class="w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-1.5 file:rounded-full file:border-0 file:bg-blue-50 file:text-[#094174] file:text-xs file:font-bold file:cursor-pointer" />
                    <img id="event-image-preview" src="#" alt=""
                        class="hidden w-full max-h-40 object-cover rounded-xl mt-2 border border-gray-200">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="toggleEventModal(false)"
                        class="flex-1 px-4 py-2 border border-gray-200 text-gray-500 font-bold rounded-full hover:bg-gray-50 transition cursor-pointer">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-[#094174] text-white font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                        {{ __('community.publish_event') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
    function switchDetailTab(targetTab) {
        ['forum', 'members', 'events', 'about'].forEach(function (tab) {
            const btn = document.getElementById('tab-' + tab);
            const content = document.getElementById('content-' + tab);
            if (!btn || !content) return;

            if (tab === targetTab) {
                btn.className = 'py-4 border-b-2 font-bold text-sm transition-all border-[#094174] text-[#094174] whitespace-nowrap cursor-pointer';
                content.classList.remove('hidden');
                if (tab === 'forum') content.classList.add('grid');
            } else {
                btn.className = 'py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap cursor-pointer';
                content.classList.add('hidden');
                if (tab === 'forum') content.classList.remove('grid');
            }
        });
    }

    function toggleEditCommunityModal(open) {
        const modal = document.getElementById('modal-edit-community');
        if (!modal) return;
        modal.classList.toggle('hidden', !open);
        document.body.style.overflow = open ? 'hidden' : '';
    }

    function previewEditImage(input, previewId) {
        if (input.files && input.files[0]) {
            document.getElementById(previewId).src = URL.createObjectURL(input.files[0]);
        }
    }

    function toggleEventModal(open) {
        const modal = document.getElementById('modal-create-event');
        if (!modal) return;
        modal.classList.toggle('hidden', !open);
        document.body.style.overflow = open ? 'hidden' : '';
    }

    function previewEventImage(input) {
        if (input.files && input.files[0]) {
            const preview = document.getElementById('event-image-preview');
            preview.src = URL.createObjectURL(input.files[0]);
            preview.classList.remove('hidden');
        }
    }

    // Deep-link a tab (?tab=events) and reopen the events modal after a
    // failed validation round-trip.
    (function () {
        const requestedTab = new URLSearchParams(window.location.search).get('tab');
        if (['forum', 'members', 'events', 'about'].includes(requestedTab)) {
            switchDetailTab(requestedTab);
        }
        @if ($eventHasErrors)
            switchDetailTab('events');
        @endif
    })();
</script>
@endsection

@extends('layouts.app') {{-- Sesuaikan dengan layout utama Sumorrow kamu --}}

@section('content')
<div class="min-h-screen bg-gray-50 pb-12 pt-24 md:pt-28">
    
    {{-- Top Navbar Bawaan Sumorrow --}}
    @include('components.navbar-light')

    {{-- Header Container --}}
    <div class="max-w-6xl mx-auto px-4 pt-6">
        
        {{-- 1. Tombol Back --}}
        <a href="{{ route('community', ['tab' => 'community']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#094174] transition mb-4 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Community
        </a>

        {{-- Community Card Profile Area --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
            
            {{-- Banner Image --}}
            <div class="h-48 md:h-64 w-full bg-gray-200 relative group/banner">
                <img id="community-banner" src="{{ $community->banner_url ? asset($community->banner_url) : asset('images/placeholder.svg') }}" class="w-full h-full object-cover" alt="Community Banner">
                {{-- Tombol Edit Banner (Hanya muncul jika admin/pembuat atau tampilkan saja untuk kebutuhan demo) --}}
                <button onclick="openImageModal('banner')" class="absolute bottom-4 right-4 bg-black/60 text-white px-3 py-1.5 rounded-xl text-xs font-medium backdrop-blur-xs opacity-0 group-hover/banner:opacity-100 transition shadow-md cursor-pointer flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                    Edit Cover
                </button>
            </div>

            {{-- Profile Details Area --}}
            <div class="px-6 pb-6 relative pt-16 md:pt-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
                
                {{-- Profile Image (Overlapping Banner) --}}
                <div class="absolute -top-16 left-6 w-28 h-28 rounded-2xl border-4 border-white bg-gray-100 shadow-md overflow-hidden group/profile">
                    <img id="community-profile" src="{{$community->image_url? asset($community->image_url) : asset('images/placeholder.svg')}}" class="w-full h-full object-cover" alt="Community Profile">
                    {{-- Tombol Edit Avatar Profil --}}
                    <button onclick="openImageModal('profile')" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/profile:opacity-100 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>

                {{-- Text Meta --}}
                <div class="md:pl-32">
                    <div>
                        <span class="text-xs text-gray-400 font-medium">Created {{ $community->created_at ? $community->created_at->format('F Y') : 'June 2026' }}</span>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-[#094174] uppercase tracking-tight mt-0.5">{{ $community->name }}</h1>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded-md tracking-wider">Public Community</span>
                            <span class="text-xs text-gray-400 font-medium">• {{ $community->members->count() }} Members</span>
                        </div>
                    </div>
                </div>

                {{-- KANAN: Daftar Foto Profil Member REAL --}}
                <div class="flex items-center gap-2 self-start md:self-auto bg-gray-50/60 p-2 rounded-2xl border border-gray-100/50">
                    <div class="flex -space-x-3 overflow-hidden">
                        @if($community->members->count() > 0)
                            @foreach($community->members->take(3) as $member)
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" 
                                    src="{{ $member->avatar_url ? asset($member->avatar_url) : asset('images/placeholder.svg') }}" 
                                    alt="{{ $member->username }}">
                            @endforeach
                        @else
                            {{-- Jika belum ada member sama sekali --}}
                            <div class="h-8 w-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[10px] text-gray-400 font-bold">?</div>
                        @endif
                    </div>
                    
                    <span class="text-[11px] text-gray-500 font-medium pr-1">
                        @if($membersCount > 0)
                            {{ $membersCount }} {{ Str::plural('Member', $membersCount) }} Joined
                        @else
                            No members yet
                        @endif
                    </span>
                </div>
            </div>

            {{-- 2. Tab Navigation (Forum, Members, Events, About) --}}
            <div class="border-t border-gray-100 px-6 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex gap-6 overflow-x-auto" id="detail-tabs">
                        <button onclick="switchDetailTab('forum')" id="tab-forum" class="py-4 border-b-2 font-bold text-sm transition-all border-[#094174] text-[#094174] cursor-pointer">Forum</button>
                        <button onclick="switchDetailTab('members')" id="tab-members" class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 cursor-pointer">Members</button>
                        <button onclick="switchDetailTab('events')" id="tab-events" class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 cursor-pointer">Events</button>
                        <button onclick="switchDetailTab('about')" id="tab-about" class="py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 cursor-pointer">About</button>
                    </div>
                    
                    {{-- Triple dot option menu --}}
                    <button class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>
                    </button>
                </div>
            </div>

        </div>

        {{-- ======================================================= --}}
        {{-- Sub-Tab Isi Konten Dinamis                             --}}
        {{-- ======================================================= --}}
        <div class="mt-6">
            
            {{-- A. SUB-TAB: FORUM --}}
            <div id="content-forum" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">

                    @include('community.components.feed', ['posts' => $posts, 'community' => $community])
                    
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2"></span> Rekomendasi Gunung
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($recommendedMountains as $mountain)
                                <a href="#" class="block group">
                                    <div class="flex items-center gap-3">
                                        {{-- Placeholder gambar jika ada --}}
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0"></div>
                                        
                                        <div>
                                            <p class="font-semibold text-gray-700 group-hover:text-blue-600 transition">
                                                {{ $mountain->name }}
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $mountain->elevation_masl }} MASL</p>
                                        </div>
                                    </div>
                                </a>
                                @if(!$loop->last) <hr class="border-gray-100"> @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- B. SUB-TAB: MEMBERS --}}
            <div id="content-members" class="hidden bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
                <h3 class="font-bold text-lg text-[#001E3A] mb-4">Squad Members ({{$community->members->count()}})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Member 1 (Admin/You) --}}
                    @forelse($community->members as $member)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                            <div class="flex items-center gap-3">
                                @if($member->avatar_url)
                                    <img src="{{ $member->avatar_url }}" class="w-10 h-10 rounded-xl object-cover" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-orange-200 flex items-center justify-center font-bold text-orange-700 text-sm">
                                        {{ strtoupper(substr($member->username, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-sm text-gray-800">
                                        {{ $member->username }} {{ auth()->id() === $member->id ? '(You)' : '' }}
                                    </h4>
                                    <p class="text-[11px] text-gray-400">@​{{ $member->username }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] {{ $community->created_by === $member->id ? 'bg-[#094174] text-white' : 'bg-gray-200 text-gray-600' }} px-2.5 py-1 rounded-full font-bold">
                                {{ $community->created_by === $member->id ? 'Leader' : 'Member' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-400 text-xs col-span-2">Tidak ada anggota.</div>
                    @endforelse
                </div>
            </div>

            {{-- C. SUB-TAB: EVENTS --}}
            <div id="content-events" class="hidden space-y-6">
            
            {{-- Header & Tombol Add (Sekarang SEMUA MEMBER BISA LIHAT & KLIK) --}}
            <div class="flex justify-between items-center bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-[#001E3A]">Upcoming Community Events</h3>
                
                {{-- Tombol ini sekarang HANYA memerlukan @auth, bukan @if(admin) --}}
                @auth
                    {{-- Tempatkan tombol ini di dekat header "Upcoming Community Events" --}}
                    <button type="button" onclick="toggleModal('event-modal')" 
                            class="bg-[#094174] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#073660] transition">
                        + Create Event
                    </button>

                    {{-- Modal Event (Pastikan di luar form lain) --}}
                    <div id="event-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
                        <form action="{{ route('community.events.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-3xl w-full max-w-md shadow-2xl">
                            @csrf
                            <h2 class="text-lg font-bold mb-4">Create New Event</h2>
                            
                            @if ($errors->any())
                                <div class="text-red-500 text-xs mb-2">
                                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                                </div>
                            @endif

                            <input type="text" name="title" placeholder="Title" class="w-full mb-3 border p-2 rounded" required>
                            <input type="datetime-local" name="event_date" class="w-full mb-3 border p-2 rounded" required>
                            <input type="text" name="location" placeholder="Location" class="w-full mb-3 border p-2 rounded" required>
                            <textarea name="description" placeholder="Description" class="w-full mb-3 border p-2 rounded" required></textarea>
                            <input type="file" name="image" class="w-full mb-3">

                            <div class="flex gap-2">
                                <button type="button" onclick="toggleModal('event-modal')" class="flex-1 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100">Cancel</button>
                                <button type="submit" class="flex-1 py-2 rounded-xl text-sm font-bold bg-[#094174] text-white">Publish</button>
                            </div>
                        </form>
                    </div>
                @else
                    <a href="{{ route('showLogin') }}" class="bg-gray-100 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-200 transition cursor-pointer">
                        Login to Create Event
                    </a>
                @endauth
            </div>

            {{-- Daftar Acara (Dynamic) --}}
            @if(isset($community->events) && $community->events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($community->events as $event)
                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <span class="text-xs font-bold text-[#094174] bg-blue-50 px-3 py-1 rounded-full">
                                        {{ $event->event_date->format('M d') }}
                                    </span>
                                    <h4 class="font-bold text-[#001E3A] text-lg mt-3 leading-tight">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1.5 font-medium flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $event->event_date->format('H:i') }} WIB
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $event->location }}
                                    </p>
                                </div>
                                @if($event->image_url)
                                    <img src="{{ asset($event->image_url) }}" alt="Event" class="w-20 h-20 rounded-2xl object-cover flex-shrink-0">
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mt-5 line-clamp-2 flex-grow">{{ $event->description }}</p>
                            
                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between gap-4">
                                <div class="text-xs text-gray-400 font-medium">
                                    Created by: {{ $event->user->username ?? 'Member' }}
                                </div>
                                <button class="bg-gray-50 text-[#094174] px-5 py-2 rounded-xl text-sm font-bold hover:bg-blue-100 transition cursor-pointer">
                                    Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Jika Kosong --}}
                <div class="bg-white p-12 rounded-3xl border border-gray-100 shadow-xs text-center">
                    <div class="w-16 h-16 bg-blue-50 text-[#094174] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="font-bold text-base text-[#001E3A]">No Upcoming Events</h3>
                    <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1">There are no climbing schedules or group meetings created for this community yet.</p>
                </div>
            @endif
        </div>
            {{-- D. SUB-TAB: ABOUT --}}
            <div id="content-about" class="hidden bg-white p-6 rounded-3xl border border-gray-100 shadow-xs space-y-4">
                <div>
                    <h3 class="font-bold text-base text-[#001E3A] mb-1">About This Community</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        {{ $community->description ?? 'Belum ada deskripsi untuk komunitas ini.' }}
                    </p>
                </div>
                <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Created By</span>
                        <span class="font-semibold text-gray-700">{{ $community->creator->username ?? 'Admin' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Category</span>
                        <span class="font-semibold text-[#094174]">{{$community->category?? 'General'}}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- ======================================================= --}}
{{-- Modal Pop-up Ganti Gambar                              --}}
{{-- ======================================================= --}}
{{-- Modal Edit Image Komunitas --}}
<div id="imageUpdateModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-800">Update Community <span id="modal-type-text">Image</span></h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('community.update-image', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="image_type" id="modal-image-type">
            
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-[#094174] transition">
                <input type="file" name="image" id="community-file-input" class="hidden" accept="image/*" required onchange="previewCommunityImage(this)">
                <div onclick="document.getElementById('community-file-input').click()" class="cursor-pointer space-y-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs text-gray-500 font-medium">Click to select image file (Max 2MB)</p>
                </div>
                <img id="community-modal-preview" src="#" class="hidden max-h-32 mx-auto rounded-lg mt-3 object-cover">
            </div>

            <button type="submit" class="w-full bg-[#094174] text-white py-2 rounded-xl text-xs font-bold hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                Save Changes
            </button>
        </form>
    </div>
    
</div>

{{-- Tambahan Javascript Pendukung --}}
<script>
    // 1. Logika Handler Tab Navigasi Sub-Menu
    function switchDetailTab(targetTab) {
        const tabs = ['forum', 'members', 'events', 'about'];
        
        tabs.forEach(tab => {
            const btn = document.getElementById('tab-' + tab);
            const content = document.getElementById('content-' + tab);
            
            if (!btn || !content) return;

            if (tab === targetTab) {
                btn.className = "py-4 border-b-2 font-bold text-sm transition-all border-[#094174] text-[#094174] cursor-pointer";
                content.classList.remove('hidden');
                if (tab === 'forum') content.classList.add('grid');
            } else {
                btn.className = "py-4 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 cursor-pointer";
                content.classList.add('hidden');
                if (tab === 'forum') content.classList.remove('grid');
            }
        });
    }

    function openImageModal(type) {
        document.getElementById('modal-image-type').value = type;
        document.getElementById('modal-type-text').innerText = type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('imageUpdateModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageUpdateModal').classList.add('hidden');
        document.getElementById('community-file-input').value = '';
        let preview = document.getElementById('community-modal-preview');
        if(preview) preview.classList.add('hidden');
        
        document.body.style.overflow = 'auto';
    }

    function previewCommunityImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('community-modal-preview');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewPostImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('post-image-preview').src = e.target.result;
                document.getElementById('post-image-preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePostImagePreview() {
        document.getElementById('post-image-input').value = '';
        document.getElementById('post-image-preview-container').classList.add('hidden');
    }

    function deleteImage(type) {
        if(confirm(`Are you sure you want to delete the community ${type} image?`)) {
            if (type === 'profile') {
                document.getElementById('community-profile').src = '/images/default-community.jpg';
            }
            alert(`${type} image reset to default successfully!`);
        }
    }

    function toggleNotification(btn) {
        const svg = btn.querySelector('svg');
        if (btn.classList.contains('text-amber-500')) {
            btn.className = "p-2.5 border border-gray-200 text-gray-400 hover:text-[#094174] hover:border-[#094174] rounded-full transition cursor-pointer";
            svg.setAttribute('fill', 'none');
        } else {
            btn.className = "p-2.5 border border-amber-200 text-amber-500 bg-amber-50 rounded-full transition cursor-pointer";
            svg.setAttribute('fill', 'currentColor');
        }
    }

    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }
</script>
@endsection
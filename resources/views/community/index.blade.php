@extends('layouts.app')

@section('content')
<div class="w-[95%] sm:w-[90%] max-w-7xl mx-auto pt-24 md:pt-32 pb-8">

    <div class="relative rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8 shadow-md">

        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2670&auto=format&fit=crop"
             alt="Mountain Background"
             class="absolute inset-0 w-full h-full object-cover z-0" />

        <div class="absolute inset-0 bg-[#03305c]/85 z-10"></div>

        <div class="relative z-20 px-6 pt-20 pb-6 md:px-12 md:pt-32 md:pb-8">
            <span class="text-white/90 text-xs md:text-sm font-bold tracking-widest uppercase mb-3 md:mb-4 block">
                Forum Discussion
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 md:mb-4 tracking-tight leading-tight max-w-3xl">
                Climber Perspective:<br class="hidden sm:block">Alpine Fluidity
            </h1>
            <p class="text-white/90 text-xs sm:text-sm md:text-base leading-relaxed max-w-3xl">
                Join the conversation on technical ascent strategies, atmospheric pressure adaptation, and the collective wisdom of the high-altitude community.
            </p>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <div class="flex items-center border-b border-gray-200 px-2 overflow-x-auto justify-between" id="forum-tabs" style="scrollbar-width: none;">
            <div class="flex items-center gap-4 sm:gap-6">
                <button
                    onclick="switchTab('explore')"
                    id="tab-explore"
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-[#094174] text-[#094174] font-bold whitespace-nowrap">
                    Explore
                </button>
                <button
                    onclick="switchTab('community')"
                    id="tab-community"
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    My Community
                </button>
            </div>

            <button
                onclick="toggleMobileSidebar()"
                class="pb-3 text-sm sm:text-base cursor-pointer text-gray-500 hover:text-[#094174] transition whitespace-nowrap flex items-center gap-1.5 lg:hidden">
                <span class="font-bold">Discover</span>
            </button>
        </div>

        <div class="py-4 md:py-6">
            <div id="content-explore" class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                <div class="col-span-1 lg:col-span-2">
                    <!-- Konten Explore Feed -->
                    @include('community.components.feed')
                </div>
                <!-- Konten Sidebar Kanan (Desktop View) -->
                <div class="hidden lg:block lg:col-span-1 border-gray-100 lg:pl-4">
                    @include('community.components.sidebar')
                </div>
            </div>

            <div id="content-community" class="hidden grid-cols-1 gap-6 md:gap-8">
                <!-- Konten My Community (Grup, Suggested Groups, dll) akan diisi di sini -->
                {{-- Top Bar: Title & Create Community Button --}}
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-heading">My Communities</h2>
                        <p class="text-gray-500 text-sm mt-1">Communities you have joined or managed.</p>
                    </div>
                    {{-- Tombol Create Community --}}
                    @auth
                        <button onclick="openModal()" class="bg-[#094174] text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-[#105DA3] transition-all shadow-md flex items-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Community
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('showLogin') }}" class="bg-[#094174] text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-[#105DA3] transition-all shadow-md flex items-center gap-2 inline-flex decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Community
                        </a>
                    @endguest
                </div>

                {{-- Main: Joined Communities Grid --}}
                @if ($myCommunities->isEmpty())
                    {{-- Empty State jika belum join grup manapun --}}
                    <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#001E3A] mb-1">You haven't joined any community yet.</h3>
                        <p class="text-gray-400 text-sm mb-4">Discover exciting climbing spaces through recommendations below!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($myCommunities as $community)
                            {{-- Menggunakan komponen reusable card bawaan timmu --}}
                            <x-community-card :community="$community" :joined="true" />
                        @endforeach
                    </div>
                @endif

                {{-- Section: Suggested For You --}}
                <div class="mt-4 border-t border-gray-100 pt-8">
                    <h3 class="text-xl font-bold text-[#001E3A] mb-4">Suggested For You</h3>
                    
                    @if ($suggestedCommunities->isEmpty())
                        <p class="text-sm text-gray-400">No new recommendations available right now.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($suggestedCommunities as $suggested)
                                <x-community-card :community="$suggested" :joined="false" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Modal: Create Community (Pop-up Form) --}}
<div id="modal-create-community" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-[#001E3A]">Create Community</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('community.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">Community Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading" placeholder="e.g., Summit Space" />
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">Description</label>
                <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading resize-none" placeholder="Describe your community..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">Privacy</label>
                <select name="privacy" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading bg-white">
                    <option value="public">Public - Anyone can join</option>
                    <option value="private">Private - Invite only</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-500 font-bold rounded-full hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-[#094174] text-white font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>


<div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/50 z-100 hidden backdrop-blur-sm transition-opacity opacity-0" onclick="toggleMobileSidebar()"></div>
    <div id="mobile-sidebar-drawer" class="fixed top-0 right-0 h-full w-[85%] sm:w-[60%] max-w-sm bg-gray-50 z-110 transform translate-x-full transition-transform duration-300 overflow-y-auto lg:hidden">
        <div class="p-5 flex items-center justify-between border-b border-gray-100 bg-white sticky top-0 z-10">
            <h2 class="font-bold text-lg text-[#1a2b4c]">Discover</h2>
            <button onclick="toggleMobileSidebar()" class="text-gray-400 hover:text-gray-600 transition p-1 bg-gray-100 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5">
            @include('community.components.sidebar')
        </div>
    </div>
    @include('community.components.modal-comment')
</div>

<style>
    #forum-tabs::-webkit-scrollbar {
        display: none;
    }
</style>

<!-- <script>
    function switchTab(tab) {
        const exploreTab = document.getElementById('tab-explore');
        const communityTab = document.getElementById('tab-community');
        const exploreContent = document.getElementById('content-explore');
        const communityContent = document.getElementById('content-community');

        if (tab === 'explore') {
            exploreTab.className = "pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-[#094174] text-[#094174] font-bold";
            communityTab.className = "pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700";
            exploreContent.classList.remove('hidden');
            exploreContent.classList.add('grid');
            communityContent.classList.add('hidden');
            communityContent.classList.remove('flex');
        } else {
            communityTab.className = "pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-[#094174] text-[#094174] font-bold";
            exploreTab.className = "pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700";
            communityContent.classList.remove('hidden');
            communityContent.classList.add('flex');
            exploreContent.classList.add('hidden');
            exploreContent.classList.remove('grid');
        }
    }

    function openModal() {
        document.getElementById('modal-create-community').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modal-create-community').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab === 'community') {
            switchTab('community');
        } else {
            switchTab('explore');
        }
    });
</script> -->

<script>
    function switchTab(tab) {
        const tabs = ['explore', 'community'];

        tabs.forEach(t => {
            const btn = document.getElementById('tab-' + t);
            const content = document.getElementById('content-' + t);
            if (!btn || !content) return;

            if (t === tab) {
                btn.className = `pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-[#094174] text-[#094174] font-bold whitespace-nowrap`;
                content.classList.remove('hidden');
                content.classList.add('grid');
            } else {
                btn.className = `pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap`;
                content.classList.add('hidden');
                content.classList.remove('grid');
            }
        });
    }

    function openModal() {
        document.getElementById('modal-create-community').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modal-create-community').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    let isMobileSidebarOpen = false;
    function toggleMobileSidebar() {
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const drawer = document.getElementById('mobile-sidebar-drawer');
    
        isMobileSidebarOpen = !isMobileSidebarOpen;

        if (isMobileSidebarOpen) {
            overlay.classList.remove('hidden');
            // small delay to allow display block to process before adding opacity
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);

            drawer.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden'; // prevent background scrolling
        } else {
            overlay.classList.add('opacity-0');
            drawer.classList.add('translate-x-full');
            document.body.style.overflow = '';
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    function toggleLike(btn) {
        const isLiked = btn.classList.contains('text-red-500');
        const svg = btn.querySelector('svg');
        const span = btn.querySelector('span');

        if (isLiked) {
            btn.classList.remove('text-red-500');
            svg.classList.remove('fill-current');
            if (span && span.classList.contains('text-red-500')) span.classList.remove('text-red-500');
        } else {
            btn.classList.add('text-red-500');
            svg.classList.add('fill-current');
            if (span) span.classList.add('text-red-500');
        }
    }

    function toggleSave(btn) {
        const svg = btn.querySelector('svg');
        const isSaved = svg.classList.contains('fill-[#094174]');

        if (isSaved) {
            svg.classList.remove('fill-[#094174]');
            btn.classList.remove('text-[#094174]');
            btn.classList.add('text-gray-500');
        } else {
            svg.classList.add('fill-[#094174]');
            btn.classList.remove('text-gray-500');
            btn.classList.add('text-[#094174]');
        }
    }

    function toggleFollow(btn) {
        const isFollowing = btn.innerText.trim() === 'Following';
        if (isFollowing) {
            // Unfollow
            btn.innerText = 'Follow';
            btn.classList.remove('bg-white', 'text-[#094174]');
            btn.classList.add('bg-[#094174]', 'text-white');
        } else {
            // Followed state
            btn.innerText = 'Following';
            btn.classList.remove('bg-[#094174]', 'text-white');
            btn.classList.add('bg-white', 'text-[#094174]');
        }
    }
    let currentActiveTag = null;

    function filterByTag(btn, tagName) {
        // Find all post elements in the feed
        const posts = document.querySelectorAll('.feed-post');

        if (currentActiveTag === tagName) {
            // Reset filter
            currentActiveTag = null;
            document.querySelectorAll('.tag-filter-btn').forEach(b => {
                b.classList.remove('bg-gray-200', 'ring-2', 'ring-[#094174]', 'ring-offset-2');
            });
            posts.forEach(post => {
                post.classList.remove('hidden');
                post.classList.add('block');
            });
        } else {
            // Apply filter
            currentActiveTag = tagName;
            document.querySelectorAll('.tag-filter-btn').forEach(b => {
                b.classList.remove('bg-gray-200', 'ring-2', 'ring-[#094174]', 'ring-offset-2');
            });
            if (btn) {
                btn.classList.add('bg-gray-200');
            }

            posts.forEach(post => {
                const postTag = post.getAttribute('data-tag');
                if (postTag === tagName) {
                    post.classList.remove('hidden');
                    post.classList.add('block');
                } else {
                    post.classList.add('hidden');
                    post.classList.remove('block');
                }
            });
        }

        // Otomatis menutup sidebar (drawer) ketika tag diklik di versi mobile
        if (typeof isMobileSidebarOpen !== 'undefined' && isMobileSidebarOpen) {
            toggleMobileSidebar();
        }
    }

    // DOMContentLoaded artinya: Tunggu sampai seluruh HTML selesai dimuat oleh browser
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Ambil URL string yang sedang aktif di browser saat ini
        const urlParams = new URLSearchParams(window.location.search);
        
        // 2. Cari tahu apakah ada parameter bernama 'tab' di dalam URL tersebut
        const activeTab = urlParams.get('tab');

        // 3. Jika parameternya ada dan isinya adalah 'community', jalankan fungsi pindah tab otomatis
        if (activeTab === 'community') {
            switchTab('community');
        } else {
            // Jika tidak ada parameter (akses normal), default ke tab explore
            switchTab('explore');
        }
    });
</script>
@endsection

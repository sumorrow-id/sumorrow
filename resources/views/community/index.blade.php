@extends('layouts.app')

@section('content')
<div class="w-[95%] sm:w-[90%] max-w-screen-xl mx-auto pt-24 md:pt-32 pb-8">

    <div class="relative rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8 shadow-md">

        {{-- Ganti URL di bawah ini dengan gambar asli nantinya --}}
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
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-[#094174] text-[#094174] font-bold whitespace-nowrap"
                >
                    Explore
                </button>
                <button
                    onclick="switchTab('community')"
                    id="tab-community"
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap"
                >
                    My Community
                </button>
            </div>

            <button
                onclick="toggleMobileSidebar()"
                class="pb-3 text-sm sm:text-base cursor-pointer text-gray-500 hover:text-[#094174] transition whitespace-nowrap flex items-center gap-1.5 lg:hidden"
            >
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
            </div>
        </div>
    </div>

    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/50 z-[100] hidden backdrop-blur-sm transition-opacity opacity-0" onclick="toggleMobileSidebar()"></div>
    <div id="mobile-sidebar-drawer" class="fixed top-0 right-0 h-full w-[85%] sm:w-[60%] max-w-sm bg-gray-50 z-[110] transform translate-x-full transition-transform duration-300 overflow-y-auto lg:hidden">
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
</script>
@endsection

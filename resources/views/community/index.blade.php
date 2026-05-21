@extends('layouts.app')

@section('content')
<div class="w-[95%] max-w-350 mx-auto pt-32 pb-8">

    <div class="relative rounded-2xl overflow-hidden mb-8 shadow-md">

        {{-- Ganti URL di bawah ini dengan gambar asli nantinya --}}
        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2670&auto=format&fit=crop"
             alt="Mountain Background"
             class="absolute inset-0 w-full h-full object-cover z-0" />

        <div class="absolute inset-0 bg-[#03305c]/85 z-10"></div>

        <div class="relative z-20 px-8 pt-24 pb-8 md:px-12 md:pt-32 md:pb-8">
            <span class="text-white/90 text-sm font-bold tracking-widest uppercase mb-4 block">
                Forum Discussion
            </span>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 tracking-tight leading-tight max-w-3xl">
                Climber Perspective:<br>Alpine Fluidity
            </h1>
            <p class="text-white/90 text-sm md:text-base leading-relaxed max-w-3xl">
                Join the conversation on technical ascent strategies, atmospheric pressure adaptation, and the collective wisdom of the high-altitude community.
            </p>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <div class="flex items-center gap-6 border-b border-gray-200 px-2" id="forum-tabs">
            <button
                onclick="switchTab('explore')"
                id="tab-explore"
                class="pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-[#094174] text-[#094174] font-bold"
            >
                Explore
            </button>
            <button
                onclick="switchTab('community')"
                id="tab-community"
                class="pb-3 border-b-2 transition-all duration-300 text-lg md:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700"
            >
                My Community
            </button>
        </div>

        <div class="py-4">
            <div id="content-explore" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <!-- Konten Explore (Feed, Filter, dll) akan diisi di sini -->
                </div>
                <div>
                    <!-- Konten Sidebar (Forum Leaders, Popular Tags, dll) akan diisi di sini -->
                </div>
            </div>

            <div id="content-community" class="hidden grid-cols-1 gap-8">
                <!-- Konten My Community (Grup, Suggested Groups, dll) akan diisi di sini -->
            </div>
        </div>
    </div>

</div>

<script>
    function switchTab(tab) {
        // Tab buttons
        const exploreTab = document.getElementById('tab-explore');
        const communityTab = document.getElementById('tab-community');

        // Content areas
        const exploreContent = document.getElementById('content-explore');
        const communityContent = document.getElementById('content-community');

        if (tab === 'explore') {
            exploreTab.className = "pb-3 border-b-2 transition-all duration-300 text-sm md:text-base cursor-pointer border-[#094174] text-[#094174] font-bold";
            communityTab.className = "pb-3 border-b-2 transition-all duration-300 text-sm md:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700";

            exploreContent.classList.remove('hidden');
            exploreContent.classList.add('grid');

            communityContent.classList.add('hidden');
            communityContent.classList.remove('grid');
        } else {
            communityTab.className = "pb-3 border-b-2 transition-all duration-300 text-sm md:text-base cursor-pointer border-[#094174] text-[#094174] font-bold";
            exploreTab.className = "pb-3 border-b-2 transition-all duration-300 text-sm md:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700";

            communityContent.classList.remove('hidden');
            communityContent.classList.add('grid');

            exploreContent.classList.add('hidden');
            exploreContent.classList.remove('grid');
        }
    }
</script>
@endsection

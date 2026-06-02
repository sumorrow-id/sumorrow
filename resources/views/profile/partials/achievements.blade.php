<div class="mt-8 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-4 sm:mb-6 gap-2 sm:gap-0">
        <h2 class="text-xl md:text-2xl font-bold text-[#0F172A]">Achievement Badges ({{ $userAchievements->count() }} / {{ $allAchievements->count() }})</h2>
        <a href="#" class="text-xs sm:text-sm font-bold text-[#2A5C9A] hover:underline">View All Collection</a>
    </div>

    <div class="flex flex-col gap-4 md:gap-5 relative">
        @forelse($allAchievements as $achievement)
            @php
                $isUnlocked = $userAchievements->has($achievement->id);
                $unlockedData = $isUnlocked ? $userAchievements->get($achievement->id) : null;
            @endphp
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 {{ $isUnlocked ? '' : 'opacity-60 grayscale' }}">
                
                <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] w-full sm:w-48 flex flex-row sm:flex-col items-center sm:justify-center gap-4">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-slate-50 flex items-center justify-center text-2xl md:text-3xl shrink-0">
                        {{ $achievement->icon_url ?? '🎖️' }}
                    </div>
                    <span class="text-[13px] md:text-xs font-bold text-[#334155] text-left sm:text-center mt-0 sm:mt-1">{{ $achievement->title }}</span>
                </div>

                <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex-1 flex flex-col justify-center">
                    <p class="text-xs md:text-[13px] text-[#2A5C9A] mb-1.5 font-bold tracking-wider flex items-center gap-2">
                        Achievement Goal
                        @if(!$isUnlocked)
                            <span class="badge bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full text-[10px] ml-2">Locked</span>
                        @endif
                    </p>
                    <p class="text-xs md:text-[13px] font-medium text-gray-500 leading-relaxed">
                        @if($isUnlocked && $unlockedData->pivot->unlocked_at)
                            <span class="text-green-600 font-semibold">Earned on {{ \Carbon\Carbon::parse($unlockedData->pivot->unlocked_at)->format('d M Y') }}.</span> <br class="block sm:hidden">
                            <span class="hidden sm:inline">-</span> 
                        @endif
                        {{ $achievement->description }}
                    </p>
                    
                    @if(!$isUnlocked)
                        <div class="mt-3 w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-gray-400 h-1.5 rounded-full w-0"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Selesaikan syarat untuk mendapatkan badge ini.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
                <p class="text-gray-500">Belum ada achievement yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>
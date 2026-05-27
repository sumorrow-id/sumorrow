<div class="mt-8 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-4 sm:mb-6 gap-2 sm:gap-0">
        <h2 class="text-xl md:text-2xl font-bold text-[#0F172A]">Achievement Badges ({{ $achievements->count() }})</h2>
        <a href="#" class="text-xs sm:text-sm font-bold text-[#2A5C9A] hover:underline">View All Collection</a>
    </div>

    <div class="flex flex-col gap-4 md:gap-5 relative">
        @forelse($achievements as $achievement)
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                <!-- Badge Card -->
                <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] w-full sm:w-48 flex flex-row sm:flex-col items-center sm:justify-center gap-4">
                    @php
                        $bgColors = [
                            'bronze' => '#FFEEDD',
                            'silver' => '#E2E8F0',
                            'gold' => '#FEF08A',
                            'diamond' => '#E0F2FE'
                        ];
                        
                        $emojis = [
                            'bronze' => '??',
                            'silver' => '??',
                            'gold' => '??',
                            'diamond' => '??'
                        ];
                        
                        $bgColor = $bgColors[$achievement->tier] ?? '#FFEEDD';
                        $icon = $achievement->icon_url ?? ($emojis[$achievement->tier] ?? '??');
                    @endphp
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center text-2xl md:text-3xl shrink-0" style="background-color: {{ $bgColor }}">
                        {{ $icon }}
                    </div>
                    <span class="text-[13px] md:text-xs font-bold text-[#334155] text-left sm:text-center mt-0 sm:mt-1">{{ $achievement->title }}</span>
                </div>

                <!-- Description Card -->
                <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex-1 flex flex-col justify-center">
                    <p class="text-xs md:text-[13px] text-[#2A5C9A] mb-1.5 uppercase font-bold tracking-wider">
                        {{ ucfirst($achievement->tier) }} Tier
                    </p>
                    <p class="text-xs md:text-[13px] font-medium text-gray-500 leading-relaxed">
                        @if($achievement->pivot->unlocked_at)
                            Earned on {{ \Carbon\Carbon::parse($achievement->pivot->unlocked_at)->format('d/m/Y') }}. <br class="block sm:hidden">
                            <span class="hidden sm:inline">-</span> 
                        @endif
                        {{ $achievement->description }}
                    </p>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
                <p class="text-gray-500">No achievements unlocked yet. Keep hiking!</p>
            </div>
        @endforelse
    </div>
</div>

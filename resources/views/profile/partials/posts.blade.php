<div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8 mt-6">

    <div class="space-y-6">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold text-[#0F172A]">Hiking History</h2>
            <a href="#" class="text-sm font-bold text-[#2A5C9A] hover:underline">View All Activities</a>
        </div>

        @forelse($posts as $post)
            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex flex-col justify-between">

                <div class="mb-6">
                    <div>
                        <div class="flex items-center justify-between gap-3 w-full">
                            <h3 class="text-[22px] font-bold text-[#0F172A] break-words">{{ $post->mountain->name ?? $post->title }}</h3>
                            @if($post->duration_days)
                                <span class="bg-[#BDE0FE] text-[#1E40AF] text-[11px] font-semibold px-3 py-1 rounded-full whitespace-nowrap">{{ $post->duration_days }}D Expedition</span>
                            @endif
                        </div>
                        <p class="text-[13px] text-gray-500 mt-1">
                            {{ $post->mountain?->province?->name ?? 'Indonesia' }} • {{ $post->climbing_date ? $post->climbing_date->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </p>
                        <div class="mt-3 text-sm text-gray-600 line-clamp-2">
                             {!! Str::markdown($post->body) !!}
                        </div>
                        
                    </div>
                </div>

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mt-auto gap-6">
                    @if($post->images->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4 w-full xl:w-[70%]">
                        @foreach($post->images->take(4) as $index => $image)
                            @if($index == 3 && $post->images->count() > 4)
                                <div class="relative w-full aspect-4/3 sm:aspect-square rounded-xl md:rounded-2xl overflow-hidden cursor-pointer group">
                                    <img src="{{ $image->image_url }}" alt="{{ $post->title }} image" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-2xl group-hover:bg-black/70 transition">+{{ $post->images->count() - 4 }}</div>
                                </div>
                            @else
                                <img src="{{ $image->image_url }}" alt="{{ $post->title }} image" class="w-full aspect-4/3 sm:aspect-square rounded-xl md:rounded-2xl object-cover">
                            @endif
                        @endforeach
                    </div>
                    @endif

                    <a href="#" class="w-full xl:w-auto text-center bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2.5 px-6 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 shrink-0">View Full Log</a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
                <p class="text-gray-500">No hiking history yet.</p>
            </div>
        @endforelse

    </div>

    <div class="lg:col-span-1 h-full">
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] h-full flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-[#0F172A] text-lg">Similar Experience</h3>
                <span class="bg-gray-100 text-gray-500 text-[10px] uppercase font-bold px-2.5 py-1 rounded-md">Recent</span>
            </div>

            <div class="grow flex flex-col gap-5">
                <!-- Person 1 -->
                <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Erik Thorne</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Semeru</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">3h ago</span>
                </div>
                <!-- Person 2 -->
                <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Anya Volkov</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Semeru</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">8h ago</span>
                </div>
                <!-- Person 3 -->
                <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Julian Chen</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Semeru</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">2d ago</span>
                </div>
                <!-- Person 4 -->
                <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Erik Thorne</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Merapi</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">3h ago</span>
                </div>
                 <!-- Person 5 -->
                 <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Anya Volkov</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Merapi</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">8h ago</span>
                </div>
                 <!-- Person 6 -->
                 <div class="flex items-center justify-between group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?q=80&w=100&fit=crop" class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-blue-500 transition-all">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition">Julian Chen</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Mount Merapi</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">2d ago</span>
                </div>
            </div>
        </div>
    </div>

</div>

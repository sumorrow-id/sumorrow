@extends('layouts.app')

@section('content')
    <div class="pt-32 bg-[#F8F9FA]">
        <div class="w-[90%] mx-auto pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1 h-fit flex flex-col gap-8">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/explore/sort.png') }}" alt="Sort Icon" class="w-5 h-5 object-contain">
                        <h2 class="font-bold text-lg text-[#001E3A]">Refine Discovery</h2>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">Difficulty</h3>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Explorer</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Adventurer</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Expert</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">Region</h3>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Java</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Sumatra</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Sulawesi</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">Papua</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">ELEVATION(MDPL)</h3>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="elevation" class="w-4 h-4 border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">0 - 3000 m</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="elevation" class="w-4 h-4 border-gray-300 text-[#094174] focus:ring-[#094174]">
                                <span class="text-sm text-[#1a2b4c] font-medium">3000 - 6000 m</span>
                            </label>
                        </div>
                    </div>

                    <button class="bg-[#094174] text-white font-bold text-sm py-2 px-6 rounded-full w-fit mt-2 hover:bg-[#105DA3] transition shadow-md">
                        Apply Filters
                    </button>
                </div>
                <div class="lg:col-span-3">
                    <div class="mb-10 ml-7">
                        <div class="flex items-center gap-3 bg-gray-200/60 rounded-full px-5 py-3 w-full">
                            <img src="{{ asset('images/explore/search.png') }}" alt="Search Icon" class="w-4 h-4 object-contain opacity-70">
                            <input type="text" placeholder="Find mountains" class="bg-transparent border-none p-0 focus:outline-none focus:ring-0 w-full text-sm text-[#1a2b4c] placeholder-gray-500 font-medium">                            </div>
                        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 ml-7">

                @foreach ($dummyMountains as $gunung)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full p-4">

                        <img src="{{ $gunung['image'] }}" alt="{{ $gunung['name'] }}" class="w-full h-56 object-cover rounded-2xl">

                        <div class="pt-5 pb-2 px-2 flex flex-col flex-grow">

                            <h3 class="font-bold text-xl text-[#1a2b4c] mb-1">{{ $gunung['name'] }}</h3>

                            <div class="flex items-center gap-1 mb-3">
                                <img src="{{ asset('images/explore/mountainelevation.png') }}" alt="Elevation" class="h-4 w-4 object-contain">
                                <span class="text-xs font-bold text-[#094174]">{{ $gunung['elevation'] }} mdpl</span>
                            </div>

                            <p class="text-xs text-gray-500 mb-4 line-clamp-3 leading-relaxed">{{ $gunung['desc'] }}</p>

                            <div class="flex flex-wrap gap-2 mb-6 mt-auto">
                                @foreach($gunung['tags'] as $tag)
                                    <span class="border border-gray-300 rounded-full px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">{{ $tag }}</span>
                                @endforeach
                            </div>

                            <a href="#" class="block w-full text-center bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-2.5 rounded-full text-sm transition shadow-md">
                                Explore Now
                            </a>
                        </div>
                    </div>
                @endforeach

            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

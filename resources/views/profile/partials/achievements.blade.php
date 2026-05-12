<div class="mt-8 px-4 sm:px-0">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-4 sm:mb-6 gap-2 sm:gap-0">
        <h2 class="text-xl md:text-2xl font-bold text-[#0F172A]">Achievement Badges</h2>
        <!-- TODO BACKEND: Tambahkan link ke route collection apabila ada (misal: route('user.achievements')) -->
        <a href="#" class="text-xs sm:text-sm font-bold text-[#2A5C9A] hover:underline">View All Collection</a>
    </div>

    <div class="flex flex-col gap-4 md:gap-5 relative">
        <!-- TODO BACKEND: Gunakan loop untuk me-render data dari database (misal: foreach($user->achievements as $achievement)) -->

        <!-- START: Template Item Achievement -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
            <!-- Badge Card -->
            <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] w-full sm:w-48 flex flex-row sm:flex-col items-center sm:justify-center gap-4">
                <!-- TODO BACKEND: Ganti badge icon/warna (misal bg-color dari $achievement->bg_color dan icon dari { $achievement->icon }}) -->
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-[#FFEEDD] flex items-center justify-center text-2xl md:text-3xl shrink-0">
                    🌋
                </div>
                <!-- TODO BACKEND: Ganti dengan nama achievement (misal { $achievement->title }}) -->
                <span class="text-[13px] md:text-xs font-bold text-[#334155] text-left sm:text-center mt-0 sm:mt-1">Volcano Master</span>
            </div>

            <!-- Description Card -->
            <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex-1 flex flex-col justify-center">
                <!-- TODO BACKEND: Ganti dengan tanggal perolehan atau deskripsi (misal Achieved on { $achievement->created_at->format('d/m/Y') }}) -->
                <p class="text-xs md:text-[13px] font-medium text-gray-500 leading-relaxed">
                    Achieved on 21/6/2024. <br class="block sm:hidden">
                    <span class="hidden sm:inline">-</span> You have successfully climbed a volcano and survived its incredible heat.
                </p>
            </div>
        </div>
    </div>
</div>

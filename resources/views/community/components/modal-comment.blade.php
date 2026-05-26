{{-- Comment Overlay Modal --}}
<div id="comment-modal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeCommentModal()"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white rounded-3xl w-[95%] max-w-lg mx-auto shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="comment-modal-content">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-lg text-[#1a2b4c]">Reply to Post</h3>
            <button onclick="closeCommentModal()" class="text-gray-400 hover:text-gray-600 transition p-1 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Original Post Snippet (Dummy) --}}
        <div class="px-6 py-4 flex gap-4">
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/dummymountain/rinjani.png') }}" class="w-10 h-10 rounded-full object-cover z-10" alt="Avatar">
                <div class="w-[2px] bg-gray-200 flex-grow mt-2"></div>
            </div>
            <div class="pb-6">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold text-[#1a2b4c] text-sm">John Doe</span>
                    <span class="text-xs text-gray-500">@realjohndoe</span>
                    <span class="text-xs text-gray-400">· 2h</span>
                </div>
                <p class="text-sm text-gray-600">Sepatu trekking yang nyaman tapi ga bikin dompet nangis apa ya?</p>
                <div class="text-xs text-gray-400 mt-2">Replying to <span class="text-[#094174]">@realjohndoe</span></div>
            </div>
        </div>

        {{-- Reply Input Area --}}
        <div class="px-6 py-4 flex gap-4">
            {{-- User Avatar --}}
            <img src="{{ asset('images/dummymountain/batur.jpg') }}" class="w-10 h-10 rounded-full object-cover shrink-0" alt="My Avatar">

            <div class="flex-grow flex flex-col">
                <textarea rows="4" placeholder="Post your reply" class="w-full bg-transparent border-none focus:ring-0 focus:outline-none resize-none text-gray-800 placeholder-gray-400 text-sm md:text-base p-0 mt-2"></textarea>

                <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-2">
                    <div class="flex items-center gap-4 text-[#094174]">
                        <button type="button" class="hover:text-[#105DA3] transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></button>
                        <button type="button" class="hover:text-[#105DA3] transition font-bold text-xs" style="font-family: monospace;">GIF</button>
                        <button type="button" class="hover:text-[#105DA3] transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                    </div>
                    <button type="button" onclick="closeCommentModal()" class="bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-1.5 px-5 rounded-full text-sm transition">
                        Reply
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function openCommentModal() {
        const modal = document.getElementById('comment-modal');
        const modalContent = document.getElementById('comment-modal-content');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Trigger animations
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeCommentModal() {
        const modal = document.getElementById('comment-modal');
        const modalContent = document.getElementById('comment-modal-content');

        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300); // Wait for transition
    }
</script>

<div id="confirm-submit-modal" class="fixed inset-0 z-200 hidden items-center justify-center px-4">
    <div id="confirm-submit-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-3xl w-full max-w-sm mx-auto shadow-2xl p-6 text-center">
        <h3 id="confirm-submit-title" class="text-lg font-bold text-[#1a2b4c] mb-2">Are you sure?</h3>
        <p id="confirm-submit-message" class="text-sm text-gray-500 mb-6"></p>
        <div class="flex items-center gap-3">
            <button type="button" id="confirm-submit-cancel"
                class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-full hover:bg-gray-50 transition">
                Cancel
            </button>
            <button type="button" id="confirm-submit-yes"
                class="flex-1 px-4 py-2.5 text-white font-bold rounded-full transition">
                Confirm
            </button>
        </div>
    </div>
</div>

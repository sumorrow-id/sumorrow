<footer class="bg-white w-full border-t border-gray-200 py-10">
    <div class="w-[95%] max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="flex flex-col gap-5 items-center md:items-start text-center md:text-left">
            <a href="/">
                <img src="{{ asset('images/logo/SUMORROW-LOGO-BLACK.png') }}" alt="{{ __('common.sumorrow_logo_alt') }}" class="h-12 w-auto" />
            </a>
            <p class="text-gray-500 text-sm leading-relaxed max-w-46.25">{{ __('common.footer_tagline') }}</p>
        </div>

        <div class="items-center md:items-start text-center md:text-left">
            <h3 class="font-bold text-[#1a2b4c] mb-6">{{ __('common.footer_navigation') }}</h3>
            <div class="flex flex-col gap-4 items-center md:items-start text-center md:text-left"">
                <a href="#" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.nav_home') }}</a>
                <a href="#" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.nav_explore') }}</a>
                <a href="#" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.nav_community') }}</a>
            </div>
        </div>

        <div class="items-center md:items-start text-center md:text-left"">
            <h3 class="font-bold text-[#1a2b4c] mb-6">{{ __('common.footer_legal') }}</h3>

            <div class="flex flex-col gap-4 items-center md:items-start text-center md:text-left"">
                <a href="#" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.footer_privacy_policy') }}</a>
                <a href="#" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.footer_terms_of_service') }}</a>
                <a href="/api/docs" class="text-gray-500 hover:text-[#094174] transition w-fit">{{ __('common.footer_api_documentation') }}</a>
            </div>
        </div>

        <div class="items-center md:items-start text-center md:text-left"">
            <h3 class="font-bold text-[#1a2b4c] mb-6">{{ __('common.footer_contact') }}</h3>

            <div class="flex flex-col gap-4 items-center md:items-start text-center md:text-left"">
                <a href="mailto:himilsquad@gmail.com" class="flex items-center gap-3 group w-fit">
                    <img src="{{ asset('images/socialmedia/email.png') }}" alt="{{ __('common.footer_email_icon_alt') }}"
                        class="w-5 h-5 object-contain" />
                    <span class="text-gray-500 group-hover:text-[#094174] transition">himilsquad@gmail.com</span>
                </a>
                <a href="#" class="flex items-center gap-3 group w-fit">
                    <img src="{{ asset('images/socialmedia/instagram.png') }}" alt="{{ __('common.footer_instagram_icon_alt') }}"
                        class="w-5 h-5 object-contain" />
                    <span class="text-gray-500 group-hover:text-[#094174] transition">himilsquad.id</span>
                </a>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-300 pt-6 mt-8 flex justify-center pb-6">
        <p class="text-gray-500 text-sm font-medium">{{ __('common.footer_copyright') }}</p>
    </div>
</footer>

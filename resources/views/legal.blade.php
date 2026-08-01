@extends ('layouts.app')

@section('content')
    @php ($sections = __("legal.$page.sections"))

    <div class="pt-32 bg-[#F8F9FA] min-h-screen">
        <div class="w-[95%] max-w-350 mx-auto pb-20">

            {{-- Hero --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 lg:p-12 mb-8">
                <div class="flex items-center gap-2 text-xs font-bold tracking-wider uppercase text-[#094174] mb-4">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#094174]"></span>
                    {{ __('common.footer_legal') }}
                </div>
                <h1 class="font-bold text-3xl lg:text-5xl text-[#001E3A] mb-4 leading-tight">{{ __("legal.$page.title") }}</h1>
                <p class="text-gray-500 text-base lg:text-lg leading-relaxed max-w-3xl">{{ __("legal.$page.intro") }}</p>
                <p class="text-sm text-gray-400 mt-6">{{ __('legal.last_updated') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-8">

                {{-- Sidebar nav --}}
                <aside class="hidden lg:block">
                    <nav class="sticky top-32 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 max-h-[calc(100vh-10rem)] overflow-y-auto">
                        <h3 class="text-xs font-bold text-gray-400 mb-4 tracking-wider uppercase">{{ __('api.on_this_page') }}</h3>
                        <ul class="flex flex-col gap-2 text-sm">
                            @foreach ($sections as $section)
                                <li>
                                    <a href="#section-{{ $loop->iteration }}"
                                        class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ $section['heading'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </aside>

                {{-- Content --}}
                <article class="flex flex-col gap-8 min-w-0">
                    @foreach ($sections as $section)
                        <section id="section-{{ $loop->iteration }}"
                            class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                            <h2 class="font-bold text-2xl text-[#001E3A] mb-3">{{ $loop->iteration }}. {{ $section['heading'] }}</h2>
                            <p class="text-gray-500 leading-relaxed">{{ $section['body'] }}</p>

                            @isset ($section['list'])
                                <ul class="list-disc pl-5 mt-4 flex flex-col gap-2 text-gray-500 leading-relaxed">
                                    @foreach ($section['list'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endisset
                        </section>
                    @endforeach
                </article>
            </div>
        </div>
    </div>
@endsection

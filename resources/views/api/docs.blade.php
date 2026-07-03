@extends ('layouts.app')

@section('content')
    <div class="pt-32 bg-[#F8F9FA] min-h-screen">
        <div class="w-[95%] max-w-350 mx-auto pb-20">

            {{-- Hero --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 lg:p-12 mb-8">
                <div class="flex items-center gap-2 text-xs font-bold tracking-wider uppercase text-[#094174] mb-4">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#094174]"></span>
                    {{ __('api.developers_label') }}
                </div>
                <h1 class="font-bold text-3xl lg:text-5xl text-[#001E3A] mb-4 leading-tight">
                    Sumorrow Public API
                    <span class="text-[#094174]">v1</span>
                </h1>
                <p class="text-gray-500 text-base lg:text-lg leading-relaxed max-w-3xl">
                    {{ __('api.hero_description') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                    <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 mb-1 tracking-wider uppercase">{{ __('api.base_url_label') }}</p>
                        <p class="text-sm font-mono font-semibold text-[#094174] break-all">/api/v1</p>
                    </div>
                    <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 mb-1 tracking-wider uppercase">{{ __('api.format_label') }}</p>
                        <p class="text-sm font-semibold text-[#1a2b4c]">{{ __('api.format_value') }}</p>
                    </div>
                    <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 mb-1 tracking-wider uppercase">{{ __('api.auth_label') }}</p>
                        <p class="text-sm font-semibold text-[#1a2b4c]">Laravel Sanctum</p>
                    </div>
                    <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 mb-1 tracking-wider uppercase">{{ __('api.rate_limit_label') }}</p>
                        <p class="text-sm font-semibold text-[#1a2b4c]">{{ __('api.authenticated_user_limit') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-8">

                {{-- Sidebar nav --}}
                <aside class="hidden lg:block">
                    <nav class="sticky top-32 bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-bold text-gray-400 mb-4 tracking-wider uppercase">{{ __('api.on_this_page') }}</h3>
                        <ul class="flex flex-col gap-2 text-sm">
                            <li><a href="#authentication"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_authentication') }}</a>
                            </li>
                            <li><a href="#rate-limiting"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_rate_limiting') }}</a></li>
                            <li><a href="#envelopes"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_envelopes') }}</a></li>
                            <li><a href="#query-params"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_query_params') }}</a></li>
                            <li><a href="#endpoints"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_endpoints') }}</a>
                            </li>
                            <li><a href="#errors"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_errors') }}</a></li>
                            <li><a href="#roadmap"
                                    class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-[#094174]/10 hover:text-[#094174] transition font-medium">{{ __('api.nav_roadmap') }}</a>
                            </li>
                        </ul>
                    </nav>
                </aside>

                {{-- Content --}}
                <div class="flex flex-col gap-8 min-w-0">

                    {{-- Authentication --}}
                    <section id="authentication"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-3">{{ __('api.nav_authentication') }}</h2>
                        <p class="text-gray-500 leading-relaxed mb-5">
                            {{ __('api.authentication_desc') }}
                        </p>

                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5 mb-5">
                            <pre class="whitespace-pre"><span class="text-[#7dd3fc]">GET</span> /api/v1/mountains <span class="text-gray-500">HTTP/1.1</span>
<span class="text-[#a7f3d0]">Host:</span> sumorrow.test
<span class="text-[#a7f3d0]">Accept:</span> application/json
<span class="text-[#a7f3d0]">Authorization:</span> Bearer 1|abc123...</pre>
                        </div>

                        <p class="text-gray-500 leading-relaxed mb-3">{{ __('api.authentication_401_note') }}
                        </p>
                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5">
                            <pre class="whitespace-pre">{ <span class="text-[#a7f3d0]">"message"</span>: <span class="text-[#fde68a]">"Unauthenticated."</span>, <span class="text-[#a7f3d0]">"status"</span>: <span class="text-[#fca5a5]">401</span> }</pre>
                        </div>
                    </section>

                    {{-- Rate Limiting --}}
                    <section id="rate-limiting"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-3">{{ __('api.nav_rate_limiting') }}</h2>
                        <p class="text-gray-500 leading-relaxed mb-5">
                            {{ __('api.rate_limiting_desc') }}
                        </p>

                        <div class="overflow-x-auto rounded-2xl border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-[#F8F9FA]">
                                    <tr>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.audience_label') }}</th>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.limit_label') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-5 py-3 text-[#1a2b4c] font-medium">{{ __('api.authenticated_user') }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.authenticated_user_limit') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 text-[#1a2b4c] font-medium">{{ __('api.unauthenticated') }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.unauthenticated_limit') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-5">
                            <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                                <p class="text-xs font-mono font-bold text-[#094174] mb-1">X-RateLimit-Limit</p>
                                <p class="text-xs text-gray-500">{{ __('api.header_bucket_size') }}</p>
                            </div>
                            <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                                <p class="text-xs font-mono font-bold text-[#094174] mb-1">X-RateLimit-Remaining</p>
                                <p class="text-xs text-gray-500">{{ __('api.header_requests_remaining') }}</p>
                            </div>
                            <div class="bg-[#F8F9FA] rounded-2xl p-4 border border-gray-100">
                                <p class="text-xs font-mono font-bold text-[#094174] mb-1">Retry-After</p>
                                <p class="text-xs text-gray-500">{{ __('api.header_retry_seconds') }}</p>
                            </div>
                        </div>
                    </section>

                    {{-- Response Envelopes --}}
                    <section id="envelopes" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-5">{{ __('api.nav_envelopes') }}</h2>

                        <h3 class="font-bold text-base text-[#1a2b4c] mb-2">{{ __('api.envelope_list') }}</h3>
                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5 mb-5">
                            <pre class="whitespace-pre">{
  <span class="text-[#a7f3d0]">"data"</span>:  [ { <span class="text-[#fde68a]">"..."</span>: <span class="text-[#fde68a]">"..."</span> } ],
  <span class="text-[#a7f3d0]">"links"</span>: { <span class="text-[#a7f3d0]">"first"</span>: <span class="text-[#fde68a]">"..."</span>, <span class="text-[#a7f3d0]">"last"</span>: <span class="text-[#fde68a]">"..."</span>, <span class="text-[#a7f3d0]">"prev"</span>: <span class="text-[#fca5a5]">null</span>, <span class="text-[#a7f3d0]">"next"</span>: <span class="text-[#fde68a]">"..."</span> },
  <span class="text-[#a7f3d0]">"meta"</span>:  { <span class="text-[#a7f3d0]">"current_page"</span>: <span class="text-[#fca5a5]">1</span>, <span class="text-[#a7f3d0]">"per_page"</span>: <span class="text-[#fca5a5]">15</span>, <span class="text-[#a7f3d0]">"total"</span>: <span class="text-[#fca5a5]">200</span> }
}</pre>
                        </div>

                        <h3 class="font-bold text-base text-[#1a2b4c] mb-2">{{ __('api.envelope_single') }}</h3>
                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5 mb-5">
                            <pre class="whitespace-pre">{ <span class="text-[#a7f3d0]">"data"</span>: { <span class="text-[#a7f3d0]">"id"</span>: <span class="text-[#fca5a5]">1</span>, <span class="text-[#a7f3d0]">"name"</span>: <span class="text-[#fde68a]">"Rinjani"</span> } }</pre>
                        </div>

                        <h3 class="font-bold text-base text-[#1a2b4c] mb-2">{{ __('api.envelope_error') }}</h3>
                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5 mb-5">
                            <pre class="whitespace-pre">{ <span class="text-[#a7f3d0]">"message"</span>: <span class="text-[#fde68a]">"Resource not found."</span>, <span class="text-[#a7f3d0]">"status"</span>: <span class="text-[#fca5a5]">404</span> }</pre>
                        </div>

                        <h3 class="font-bold text-base text-[#1a2b4c] mb-2">{{ __('api.envelope_validation') }}</h3>
                        <div
                            class="rounded-2xl bg-[#0b1a2e] text-[#cfd8e3] font-mono text-xs lg:text-sm overflow-x-auto p-5">
                            <pre class="whitespace-pre">{
  <span class="text-[#a7f3d0]">"message"</span>: <span class="text-[#fde68a]">"The selected difficulty is invalid."</span>,
  <span class="text-[#a7f3d0]">"status"</span>: <span class="text-[#fca5a5]">422</span>,
  <span class="text-[#a7f3d0]">"errors"</span>: { <span class="text-[#a7f3d0]">"difficulty"</span>: [ <span class="text-[#fde68a]">"The selected difficulty is invalid."</span> ] }
}</pre>
                        </div>
                    </section>

                    {{-- Query Parameters --}}
                    <section id="query-params"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-3">{{ __('api.nav_query_params') }}</h2>
                        <p class="text-gray-500 leading-relaxed mb-5">
                            <span class="font-mono text-[#094174] font-semibold">GET /mountains</span> {{ __('api.query_params_intro_1') }}
                            <span class="font-mono text-[#094174] font-semibold">GET /provinces</span> {{ __('api.query_params_intro_2') }} <span
                                class="font-mono">search</span>, <span class="font-mono">page</span>, <span
                                class="font-mono">limit</span> {{ __('api.query_params_default_note') }}
                        </p>

                        <div class="overflow-x-auto rounded-2xl border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-[#F8F9FA]">
                                    <tr>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.param_label') }}</th>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.type_label') }}</th>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.notes_label') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-[#1a2b4c]">
                                    <tr>
                                        <td class="px-5 py-3 font-mono">search</td>
                                        <td class="px-5 py-3 text-gray-500">string &le;120</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.search_notes') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">province_id</td>
                                        <td class="px-5 py-3 text-gray-500">int</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.province_notes') }} <span
                                                class="font-mono">provinces</span></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">difficulty</td>
                                        <td class="px-5 py-3 text-gray-500">enum</td>
                                        <td class="px-5 py-3 text-gray-500">easy &middot; moderate &middot; hard &middot;
                                            strenuous</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">is_active</td>
                                        <td class="px-5 py-3 text-gray-500">bool</td>
                                        <td class="px-5 py-3 text-gray-500">true / false / 1 / 0</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">sort</td>
                                        <td class="px-5 py-3 text-gray-500">enum</td>
                                        <td class="px-5 py-3 text-gray-500">name ({{ __('api.default_label') }}) &middot; avg_rating &middot;
                                            elevation_masl</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">order</td>
                                        <td class="px-5 py-3 text-gray-500">enum</td>
                                        <td class="px-5 py-3 text-gray-500">asc ({{ __('api.default_label') }}) &middot; desc</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">page</td>
                                        <td class="px-5 py-3 text-gray-500">int &ge;1</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.page_notes') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono">limit</td>
                                        <td class="px-5 py-3 text-gray-500">int 1..50</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.limit_notes') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- Endpoints --}}
                    <section id="endpoints"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-5">{{ __('api.nav_endpoints') }}</h2>

                        @php
                            $groups = [
                                'Provinces' => [
                                    [
                                        'method' => 'GET',
                                        'path' => '/provinces',
                                        'desc' => 'List provinces with mountains_count',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/provinces/{id}',
                                        'desc' => 'Single province detail',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/provinces/{id}/mountains',
                                        'desc' => 'Paginated mountains in a province',
                                    ],
                                ],
                                'Mountains' => [
                                    [
                                        'method' => 'GET',
                                        'path' => '/mountains',
                                        'desc' => 'List mountains (filter / sort / paginate)',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/mountains/{id}',
                                        'desc' => 'Full mountain detail with counts',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/mountains/{id}/images',
                                        'desc' => 'Image gallery (ordered by position)',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/mountains/{id}/basecamps',
                                        'desc' => 'Basecamps for a mountain',
                                    ],
                                    [
                                        'method' => 'GET',
                                        'path' => '/mountains/{id}/ratings',
                                        'desc' => 'Paginated ratings with author',
                                    ],
                                ],
                                'Basecamps' => [
                                    [
                                        'method' => 'GET',
                                        'path' => '/basecamps/{id}',
                                        'desc' => 'Basecamp detail with parent mountain',
                                    ],
                                ],
                                'Auth context' => [
                                    ['method' => 'GET', 'path' => '/user', 'desc' => 'Authenticated user info'],
                                    [
                                        'method' => 'GET',
                                        'path' => '/check-verification',
                                        'desc' => 'Email verification status',
                                    ],
                                ],
                            ];
                        @endphp

                        <div class="flex flex-col gap-6">
                            @foreach ($groups as $title => $items)
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 mb-3 tracking-wider uppercase">
                                        {{ $title }}</h3>
                                    <div class="flex flex-col gap-2">
                                        @foreach ($items as $ep)
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center gap-3 bg-[#F8F9FA] rounded-2xl border border-gray-100 px-4 py-3">
                                                <span
                                                    class="inline-flex items-center justify-center bg-[#094174] text-white text-xs font-bold rounded-full px-3 py-1 w-fit">{{ $ep['method'] }}</span>
                                                <span
                                                    class="font-mono text-sm text-[#001E3A] font-semibold break-all">/api/v1{{ $ep['path'] }}</span>
                                                <span class="text-sm text-gray-500 sm:ml-auto">{{ $ep['desc'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Error Codes --}}
                    <section id="errors"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-5">{{ __('api.nav_errors') }}</h2>
                        <div class="overflow-x-auto rounded-2xl border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-[#F8F9FA]">
                                    <tr>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.code_label') }}</th>
                                        <th class="text-left font-bold text-[#001E3A] px-5 py-3">{{ __('api.when_label') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-5 py-3 font-mono font-bold text-[#094174]">401</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.error_401_desc') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono font-bold text-[#094174]">404</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.error_404_desc') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono font-bold text-[#094174]">422</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.error_422_desc') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-mono font-bold text-[#094174]">429</td>
                                        <td class="px-5 py-3 text-gray-500">{{ __('api.error_429_desc') }} <span
                                                class="font-mono">Retry-After</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- Roadmap --}}
                    <section id="roadmap"
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 scroll-mt-32">
                        <h2 class="font-bold text-2xl text-[#001E3A] mb-3">{{ __('api.nav_roadmap') }}</h2>
                        <p class="text-gray-500 leading-relaxed mb-5">
                            {{ __('api.roadmap_desc') }}
                        </p>
                        <ul class="flex flex-col gap-2 text-sm text-[#1a2b4c]">
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span><span
                                        class="font-mono font-semibold">POST /mountains/{id}/ratings</span> &mdash; {{ __('api.roadmap_submit_rating') }}</span></li>
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span><span
                                        class="font-mono font-semibold">POST / PATCH / DELETE
                                        /mountains/{id}/comments</span> &mdash; {{ __('api.roadmap_comment_lifecycle') }}</span></li>
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span>{{ __('api.roadmap_forum_endpoints') }} &mdash; <span class="font-mono">/posts</span>, {{ __('api.roadmap_replies') }}</span></li>
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span>{{ __('api.roadmap_user_endpoints') }} &mdash; <span class="font-mono">/auth/register</span>, <span
                                        class="font-mono">/auth/login</span>, <span
                                        class="font-mono">/users/me</span></span></li>
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span>{{ __('api.roadmap_regions') }}</span></li>
                            <li class="flex items-start gap-3"><span
                                    class="mt-1.5 inline-block w-1.5 h-1.5 rounded-full bg-[#094174] shrink-0"></span><span>{{ __('api.roadmap_tokens') }} &mdash; <span class="font-mono">POST
                                        /api/v1/auth/tokens</span></span></li>
                        </ul>
                    </section>

                </div>
            </div>
        </div>
    </div>
@endsection

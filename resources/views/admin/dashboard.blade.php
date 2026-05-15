@extends('layouts.admin')

@section('title', 'Dashboard - Sumorrow Admin')
@section('page_title', 'Dashboard')

@section('admin_content')
<header class="mb-12">
    <h1 class="text-4xl font-extrabold text-heading mt-2">Control Center</h1>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Pending Reports</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">12</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">New Users</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">148</h3>
    </div>
    <div class="bg-white p-8 rounded-3xl border border-morning-mist shadow-sm">
        <p class="text-xs font-bold text-lithic-blue uppercase tracking-widest mb-1">Forum Posts</p>
        <h3 class="text-3xl font-extrabold text-deep-midnight">2,401</h3>
    </div>
</div>

<!-- Moderation Table -->
<section class="bg-white rounded-3xl border border-morning-mist shadow-sm overflow-hidden">
    <div class="p-8 border-b border-morning-mist flex justify-between items-center">
        <h3 class="text-xl font-bold text-deep-midnight">Recent Forum Activity</h3>
        <button class="text-sm font-semibold text-summit-blue hover:underline">View All</button>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="bg-morning-mist/30">
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">User</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Content</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest">Status</th>
                <th class="px-8 py-4 text-[10px] font-bold text-lithic-blue uppercase tracking-widest text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-morning-mist">
            <!-- Example Row -->
            <tr>
                <td class="px-8 py-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-surface"></div>
                        <span class="text-sm font-bold text-deep-midnight">@pendaki_ceria</span>
                    </div>
                </td>
                <td class="px-8 py-6">
                    <p class="text-sm text-lithic-blue line-clamp-1">"Tips mendaki Rinjani saat musim hujan..."</p>
                </td>
                <td class="px-8 py-6">
                    <span class="px-3 py-1 text-[10px] font-bold bg-sky-oxygen/20 text-sky-oxygen rounded-full uppercase">Pending</span>
                </td>
                <td class="px-8 py-6 text-right">
                    <button class="text-sm font-semibold bg-summit-blue text-white px-4 py-2 rounded-lg hover:bg-deep-midnight transition">Moderate</button>
                </td>
            </tr>
        </tbody>
    </table>
</section>
@endsection
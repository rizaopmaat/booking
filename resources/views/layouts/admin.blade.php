@extends('layouts.app')

@section('layout-content')
<div class="flex h-screen bg-gray-100">
    {{-- Sidebar (optional) --}}
    <aside class="w-64 bg-gray-800 text-white p-6 hidden md:block">
        <h2 class="text-xl font-bold mb-6">{{ __('Admin Panel') }}</h2>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.rooms*') ? 'bg-gray-700' : '' }}">
                {{ __('Kamers Beheren') }}
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.bookings*') ? 'bg-gray-700' : '' }}">
                {{ __('Boekingen Beheren') }}
            </a>
            <a href="{{ route('admin.booking-options.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.booking-options*') ? 'bg-gray-700' : '' }}">
                {{ __('Opties Beheren') }}
            </a>
            {{-- Add more admin links as needed --}}
        </nav>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar (optional) --}}
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <div>{{-- Breadcrumbs or Title --}}</div>
            <div>
                {{-- User menu, logout etc. --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Uitloggen') }}
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
             @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content') {{-- This is where the specific admin page content will go --}}
        </div>
    </main>
</div>

{{-- Ensure Alpine.js is included - it might already be in layouts.app --}}
{{-- If not, add: <script src="//unpkg.com/alpinejs" defer></script> --}}
@endsection

{{-- Override the default content section from layouts.app --}}
@section('content')
    {{-- Leave this empty or remove if layouts.app doesn't have a content section you need to override --}}
@endsection 
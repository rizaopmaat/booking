@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Dashboard') }}</h1>

    <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700">{{ __('Welcome, Admin!') }}</h2>
        <p class="text-gray-600">{{ __('Manage your hotel operations from here.') }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Total Rooms --}}
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 uppercase">{{ __('Total Rooms') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalRooms ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Active Bookings --}}
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 uppercase">{{ __('Active Bookings') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $activeBookings ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Occupancy Rate --}}
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 uppercase">{{ __('Occupancy Rate (Today)') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $occupancyRate ?? 0 }}%</p>
            </div>
        </div>
    </div>

    {{-- Recent Activity & Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Activity --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">{{ __('Recent Activity') }}</h3>
            
            @if($recentBookings->isEmpty())
                <p class="text-gray-500 py-4">{{ __('No recent activity to display.') }}</p>
            @else
                <ul class="space-y-4">
                    @foreach($recentBookings as $booking)
                    <li class="flex items-center space-x-3">
                        <span class="bg-red-100 p-2 rounded-full">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </span>
                        <p class="text-sm text-gray-700">
                            {{ __('New booking') }} - {{ $booking->room->name }} ({{ $booking->user->name }})
                        </p>
                        <span class="text-xs text-gray-400 ml-auto">{{ $booking->created_at->diffForHumans() }}</span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Quick Links --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">{{ __('Quick Links') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-red-50 rounded-lg transition duration-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="text-gray-700 font-medium">{{ __('Manage Rooms') }}</span>
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-red-50 rounded-lg transition duration-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-gray-700 font-medium">{{ __('Manage Bookings') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-red-50 rounded-lg transition duration-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-gray-700 font-medium">{{ __('Manage Users') }}</span>
                </a>
                <a href="{{ route('admin.booking-options.index') }}" class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-red-50 rounded-lg transition duration-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="text-gray-700 font-medium">{{ __('Manage Options') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 
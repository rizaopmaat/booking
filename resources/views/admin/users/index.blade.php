@extends('layouts.admin')

@section('title', __('Gebruikersbeheer'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Gebruikersbeheer') }}</h1>
    </div>

    {{-- Zoek en filter --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-grow md:flex-grow-0">
                <input type="text" name="search" placeholder="{{ __('Zoeken op naam of email...') }}" value="{{ request('search') }}" 
                    class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500">
            </div>
            
            <div>
                <select name="is_admin" class="px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500">
                    <option value="">{{ __('Alle gebruikers') }}</option>
                    <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>{{ __('Alleen admins') }}</option>
                    <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>{{ __('Alleen normale gebruikers') }}</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition">
                    {{ __('Zoeken') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition">
                    {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>

    {{-- Foutmeldingen --}}
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Succes meldingen --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Gebruikers tabel --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Naam') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('E-mail') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Rol') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Aangemaakt') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Acties') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $user->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_admin)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('Admin') }}
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ __('Gebruiker') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">
                                {{ __('Bewerken') }}
                            </a>
                            
                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ $user->is_admin ? __('messages.confirm_revoke_admin') : __('messages.confirm_make_admin') }}')">
                                    {{ $user->is_admin ? __('Admin rechten intrekken') : __('Maak admin') }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Weet je zeker dat je deze gebruiker wilt verwijderen?') }}')">
                                    {{ __('Verwijderen') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">
                        {{ __('Geen gebruikers gevonden') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginering --}}
    <div class="mt-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection 
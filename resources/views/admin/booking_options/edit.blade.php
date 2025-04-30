@extends('layouts.admin') {{-- Assuming you have an admin layout --}}

@section('title', __('Edit Booking Option') . ' - ' . $option->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('Edit Booking Option') }}: {{ $option->name }}</h1>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('admin.options.update', $option) }}" method="POST">
            @method('PUT')
            @include('admin.booking_options._form', ['option' => $option, 'submitText' => __('Update Option')])
        </form>
    </div>
</div>
@endsection 
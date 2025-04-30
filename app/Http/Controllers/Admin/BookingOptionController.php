<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingOption;
use Illuminate\Http\Request;

class BookingOptionController extends Controller
{
    public function index()
    {
        $options = BookingOption::latest()->paginate(15);
        return view('admin.booking_options.index', compact('options'));
    }

    public function create()
    {
        return view('admin.booking_options.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $validated['is_cancellation_option'] = $request->has('is_cancellation_option');
        $validated['is_active'] = $request->has('is_active');

        BookingOption::create($validated);

        return redirect()->route('admin.options.index')->with('success', __('Booking option created successfully.'));
    }

    public function show(BookingOption $option)
    {
        return redirect()->route('admin.options.edit', $option);
    }

    public function edit(BookingOption $option)
    {
        return view('admin.booking_options.edit', compact('option'));
    }

    public function update(Request $request, BookingOption $option)
    {
        $validated = $request->validate($this->validationRules());

        $validated['is_cancellation_option'] = $request->has('is_cancellation_option');
        $validated['is_active'] = $request->has('is_active');

        $option->update($validated);

        return redirect()->route('admin.options.index')->with('success', __('Booking option updated successfully.'));
    }

    public function destroy(BookingOption $option)
    {
        try {
            $option->delete();
            return redirect()->route('admin.options.index')->with('success', __('Booking option deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('admin.options.index')->with('error', __('Could not delete booking option.') . ' ' . $e->getMessage());
        }
    }

    protected function validationRules(): array
    {
        return [
            'name' => 'required|array',
            'name.nl' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.nl' => 'nullable|string',
            'description.en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,per_guest',
            'is_cancellation_option' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}

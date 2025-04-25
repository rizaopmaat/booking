<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingOption;
use Illuminate\Http\Request;

class BookingOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $options = BookingOption::latest()->paginate(15);
        return view('admin.booking_options.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.booking_options.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $validated['is_cancellation_option'] = $request->has('is_cancellation_option');
        $validated['is_active'] = $request->has('is_active');

        BookingOption::create($validated);

        return redirect()->route('admin.booking-options.index')->with('success', __('Booking option created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingOption $option)
    {
        return redirect()->route('admin.booking-options.edit', $option);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookingOption $option)
    {
        return view('admin.booking_options.edit', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookingOption $option)
    {
        $validated = $request->validate($this->validationRules());

        $validated['is_cancellation_option'] = $request->has('is_cancellation_option');
        $validated['is_active'] = $request->has('is_active');

        $option->update($validated);

        return redirect()->route('admin.booking-options.index')->with('success', __('Booking option updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingOption $option)
    {
        try {
            $option->delete();
            return redirect()->route('admin.booking-options.index')->with('success', __('Booking option deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('admin.booking-options.index')->with('error', __('Could not delete booking option.') . ' ' . $e->getMessage());
        }
    }

    /**
     * Defines validation rules for store and update.
     */
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
            'price_type' => 'required|in:fixed,per_person',
            'is_cancellation_option' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}

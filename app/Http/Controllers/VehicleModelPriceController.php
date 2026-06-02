<?php

namespace App\Http\Controllers;

use App\Models\VehicleModelPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleModelPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vehicle-model-prices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicle-model-prices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'model'         => 'required|string|max:255|unique:vehicle_model_prices,model',
            'day_rent'      => 'required|numeric|min:0',
            'rate_per_km'   => 'required|numeric|min:0',
            'hourly_charge' => 'required|numeric|min:0',
            'local_charge'  => 'required|numeric|min:0',
        ]);

        VehicleModelPrice::create($validated);

        return redirect()->route('vehicle-model-prices.index')->with('success', 'Vehicle model price created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleModelPrice $vehicleModelPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleModelPrice $vehicleModelPrice)
    {
        return view('vehicle-model-prices.edit', compact('vehicleModelPrice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleModelPrice $vehicleModelPrice)
    {
        $validated = $request->validate([
            'model'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicle_model_prices', 'model')->ignore($vehicleModelPrice->id),
            ],
            'day_rent'      => 'required|numeric|min:0',
            'rate_per_km'   => 'required|numeric|min:0',
            'hourly_charge' => 'required|numeric|min:0',
            'local_charge'  => 'required|numeric|min:0',
        ]);

        $vehicleModelPrice->update($validated);

        return redirect()->route('vehicle-model-prices.index')->with('success', 'Vehicle model price updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleModelPrice $vehicleModelPrice)
    {
        $vehicleModelPrice->delete();

        return redirect()->route('vehicle-model-prices.index')->with('success', 'Vehicle model price deleted successfully.');
    }
}

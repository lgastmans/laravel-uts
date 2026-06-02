<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Vehicle;
use App\Models\VehicleModelPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vehicles.index', [
            'owners' => Owner::orderBy('name')->get(),
            'vehicleModelPrices' => VehicleModelPrice::orderBy('model')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create', [
            'owners' => Owner::orderBy('name')->get(),
            'vehicleModelPrices' => VehicleModelPrice::orderBy('model')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id'               => 'nullable|exists:owners,id',
            'vehicle_model_price_id' => 'required|exists:vehicle_model_prices,id',
            'registration_number'    => 'required|string|max:255|unique:vehicles,registration_number',
            'insurance_validity'     => 'nullable|date',
            'permit_validity'        => 'nullable|date',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'owners' => Owner::orderBy('name')->get(),
            'vehicleModelPrices' => VehicleModelPrice::orderBy('model')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'owner_id'               => 'nullable|exists:owners,id',
            'vehicle_model_price_id' => 'required|exists:vehicle_model_prices,id',
            'registration_number'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicles', 'registration_number')->ignore($vehicle->id),
            ],
            'insurance_validity'     => 'nullable|date',
            'permit_validity'        => 'nullable|date',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}

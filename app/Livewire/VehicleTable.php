<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class VehicleTable extends PowerGridComponent
{
    public string $tableName = 'vehicle-table-geodta-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Vehicle::query()->with(['owner', 'vehicleModelPrice']);
    }

    public function header(): array
    {
        return [
            Button::add('create_vehicle')
                ->slot('Add vehicle')
                ->class('text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none')
                ->dispatch('open-vehicle-create-modal', [])
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('registration_number')
            ->add('model', fn (Vehicle $vehicle) => $vehicle->vehicleModelPrice?->model)
            ->add('owner_name', fn (Vehicle $vehicle) => $vehicle->owner?->name)
            ->add('insurance_validity', fn (Vehicle $vehicle) => $vehicle->insurance_validity?->format('Y-m-d'))
            ->add('permit_validity', fn (Vehicle $vehicle) => $vehicle->permit_validity?->format('Y-m-d'))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(),

            Column::make('Registration number', 'registration_number')
                ->sortable()
                ->searchable(),

            Column::make('Model', 'model'),

            Column::make('Owner', 'owner_name'),

            Column::make('Insurance validity', 'insurance_validity')
                ->sortable()
                ->searchable(),

            Column::make('Permit validity', 'permit_validity')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('delete-vehicle')]
    public function deleteVehicle(int $vehicleId): void
    {
        Vehicle::findOrFail($vehicleId)->delete();

        session()->flash('success', 'Vehicle deleted successfully.');
        $this->redirect(route('vehicles.index'), navigate: true);
    }

    public function actions(Vehicle $row): array
    {
        return [
            Button::make('edit', '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>')
                ->class('inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500')
                ->route('vehicles.edit', ['vehicle' => $row]),

            Button::make('delete', '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>')
                ->class('inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500')
                ->dispatch('delete-vehicle', ['vehicleId' => $row->id]),
        ];
    }
}

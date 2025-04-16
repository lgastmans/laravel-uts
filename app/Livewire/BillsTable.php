<?php

namespace App\Livewire;

use App\Models\Bill;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BillsTable extends PowerGridComponent
{
    public string $tableName = 'bills';

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
        return Bill::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('bill_id')
            ->add('bill_number')
            ->add('bill_date_formatted', fn (Bill $model) => Carbon::parse($model->bill_date)->format('d/m/Y H:i:s'))
            ->add('customer')
            ->add('from_place')
            ->add('to_place')
            ->add('dep_date_formatted', fn (Bill $model) => Carbon::parse($model->dep_date)->format('d/m/Y'))
            ->add('dep_time')
            ->add('arr_date_formatted', fn (Bill $model) => Carbon::parse($model->arr_date)->format('d/m/Y'))
            ->add('arr_time')
            ->add('vehicle_reg_no')
            ->add('amount')
            ->add('car')
            ->add('driver_id')
            ->add('zoho_invoice_id')
            ->add('synced_at')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(),

            Column::make('Bill id', 'bill_id')
                ->hidden(),

            Column::make('Bill number', 'bill_number')
                ->sortable()
                ->searchable(),

            Column::make('Bill date', 'bill_date_formatted', 'bill_date')
                ->sortable(),

            Column::make('Customer', 'customer')
                ->sortable()
                ->searchable(),

            Column::make('From place', 'from_place')
                ->sortable()
                ->searchable(),

            Column::make('To place', 'to_place')
                ->sortable()
                ->searchable(),

            Column::make('Dep date', 'dep_date_formatted', 'dep_date')
                ->sortable(),

            Column::make('Dep time', 'dep_time')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make('Arr date', 'arr_date_formatted', 'arr_date')
                ->sortable(),

            Column::make('Arr time', 'arr_time')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make('Vehicle reg no', 'vehicle_reg_no')
                ->sortable()
                ->searchable(),

            Column::make('Amount', 'amount')
                ->sortable()
                ->searchable(),

            Column::make('Car', 'car')
                ->sortable()
                ->searchable(),

            Column::make('Driver id', 'driver_id')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make('Zoho invoice id', 'zoho_invoice_id')
                ->sortable()
                ->searchable(),

            Column::make('Synced at', 'synced_at_formatted', 'synced_at')
                ->sortable(),

            Column::make('Synced at', 'synced_at')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable()
                ->hidden(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datetimepicker('bill_date'),
            Filter::datepicker('dep_date'),
            Filter::datepicker('arr_date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Bill $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}

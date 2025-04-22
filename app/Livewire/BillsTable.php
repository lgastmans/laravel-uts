<?php

namespace App\Livewire;

use App\Models\Bill;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use App\Services\ZohoApiService;

final class BillsTable extends PowerGridComponent
{
    public string $tableName = 'bills';

    public array $selected = [];

    public ?string $accessToken = null;

    public ZohoApiService $zoho;



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
    
    public function header(): array
    {
        return [
            Button::add('export_to_zoho')
                ->slot('Export to Zoho')
                ->class('text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800')
                ->dispatch('exportSelectedBills', [])
        ];
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
            ->add('synced_at_formatted', fn ($bill) => optional($model->synced_at)->format('d-m-Y H:i'))
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
                ->searchable()
                ->hidden(),

            Column::make('Driver id', 'driver_id')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make('Zoho invoice id', 'zoho_invoice_id')
                ->sortable()
                ->searchable()
                ->hidden(),

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
                ->hidden()
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('bill_number'),
            Filter::datepicker('bill_date'),
            Filter::inputText('customer'),
            Filter::datepicker('dep_date'),
            Filter::datepicker('arr_date'),
            Filter::inputText('vehicle_reg_no'),
        ];
    }

    #[\Livewire\Attributes\On('exportSelectedBills')]
    public function exportSelectedBills(): void
    {
        /*
          #attributes: array:19 [
            "id" => 3
            "bill_id" => 212340
            "bill_number" => "3"
            "bill_date" => "2024-04-01 00:00:00"
            "customer" => "STEPHANIE"
            "from_place" => "AUROVILLE"
            "to_place" => "PONDY & BACK"
            "dep_date" => null
            "dep_time" => null
            "arr_date" => null
            "arr_time" => null
            "vehicle_reg_no" => "TN-85-L-5683"
            "amount" => "780.00"
            "car" => null
            "driver_id" => 135
            "zoho_invoice_id" => null
            "synced_at" => null
            "created_at" => "2025-04-15 11:19:30"
            "updated_at" => "2025-04-15 11:19:30"
          ]
        */

        if (empty($this->checkboxValues)) {
            $messages[] = 'Please select at least one bill to export.';
            $this->dispatch('showBulkMessages', [
                'type' => 'info',
                'title' => 'Warnings:',
                'messages' => $messages
            ]);
        }

        $zoho = new ZohoApiService();

        // retrieve the "Transport Charges" item from Zoho
        $item = $zoho->searchItemByName("Transport Charges");

        if ($item && !empty($item['success'])) {

            /*
            $line_items = [
                'item_id'           => $item['success'],
                //'product_type'    => "services",
                "rate"              => (double)$rate,
                "description"       => (string)trim($itemName),
                // "tax_id"         => (string)$zohoTaxIDs['intra'],
                // "tax_name"       => 'GST',
                // "tax_percentage" => (float)$taxPercentage,
                //"hsn_or_sac"      => (string)$hsn,
            ];
            */

            $bills = Bill::all();
            $messages = [];
            foreach ($this->checkboxValues as $bill_id) {

                $bill = $bills->find($bill_id);

                // bill_id not found in local database
                if (!$bill) {
                    Log::info('Bill not found, ID: ', [$bill_id]);
                    $messages[] = 'Bill ID '.$bill_id.' not found.';
                }

                // skip, zoho_invoice_id is set in local database, already exists in Zoho
                if ($bill->zoho_invoice_id && !empty($bill->zoho_invoice_id)) {
                    $messages[] = 'Invoice '.$bill->bill_number.' already exported to Zoho';
                    continue;
                }

                // check whether invoice with given reference exists in Zoho
                /*
                $reference_number = config('services.zoho.invoice_prefix')."/".$bill->bill_number;
                $result = $zoho->getZohoInvoiceByReference($reference_number);

                if ($result && !empty($result['success'])) {
                    $messages[] = "An invoice with Reference Number $reference_number already exists in Zoho";
                }
                */

                // get or create customer
                $customer = $zoho->getOrCreateVendor($bill->customer);

                if (isset($customer['success'])) {
                    $zoho_customer_id = $customer['success'];

                    $description = "From: ".$bill->from_place." ".$bill->dep_date." ".$bill->dep_time."\nTo: ".$bill->to_place." ".$bill->arr_date." ".$bill->arr_time;

                    // invoice details
                    $invoice_data = [
                        'customer_id'       => $zoho_customer_id,
                        'reference_number'  => $bill->bill_number,
                        'is_inclusive_tax'  => false,
                        'date'              => Carbon::parse($bill->bill_date)->format('Y-m-d'), 
                        'line_items'        => [
                            [
                                'item_id'           => $item['success'],
                                //'product_type'    => "services",
                                "rate"              => (double)$bill->amount,
                                "quantity"          => (float)1,
                                "description"       => (string)trim($description),
                            ]
                        ]
                    ];

                    // create invoice in Zoho
                    $invoice = $zoho->createInvoice($invoice_data);

                    if ($invoice && (!empty($invoice['success']))) {
                        $bill->zoho_invoice_id = $invoice['success'];
                        $bill->zoho_customer_id = $zoho_customer_id;
                        $bill->synced_at = now();
                        $bill->save();

                        $messages[] = "Invoice ".$bill->bill_number." successfully export to Zoho";

                        $this->dispatch('refreshTable');
                    }
                    else {
                        $messages[] = $invoice['error'];
                        Log::error($invoice);
                    }
                } else {
                    Log::error($customer);
                    $messages[] = 'Could not create customer '.$bill->customer.' in Zoho';
                }

            } // foreach
        }
        else {
            Log::error($item);
            $messages[] = 'Could not retrieve the Transport Charges item ';
        }

        if (!empty($messages)) {
            $this->dispatch('showBulkMessages', [
                'type' => 'info',
                'title' => 'Warnings:',
                'messages' => $messages
            ]);
        }

        // loop through the invoices
        // get each invoice details 
        // export to zoho
        
        // dd($this->accessToken);

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

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'refreshTable',
            ]
        );
    }

    public function refreshTable(): void
    {
        $this->dispatch('pg:eventRefresh-default');
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

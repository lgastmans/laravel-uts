<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Data structure of bills extracted from Paradox table
 * {
        "BillId": "212338",
        "BillNumber": "1",
        "BillDate": "2024-04-01 00:00:00",
        "BillMonth": "4",
        "IsAccountBill": "0",
        "DriverId": "186",
        "BillType": "Agency Credit Bill",
        "Customer": "GUY",
        "AccountNumber": "-1",
        "AccountName": null,
        "Community": "AUROVILLE",
        "FromPlace": "AUROVILLE",
        "ToPlace": "QUITE",
        "DepDate": "2024-04-01 00:00:00",
        "DepTime": "11:45 AM",
        "ArrDate": "2024-04-01 00:00:00",
        "ArrTime": "12:45 PM",
        "ArrTime2": "1899-12-30 12:45:00",
        "VehicleRegNo": "TN-15-V-7995",
        "Amount": "505.0",
        "Pending": "0",
        "ReceiverId": "11",
        "Reversed": "0",
        "Cancelled": "0",
        "Car": "TOYOTA ETYOS",
        "ReceivedDate": "2024-04-02 00:00:00",
        "HasHillCharge": "0",
        "HasPermitCharge": "0",
        "Advance": "0.0",
        "HasLocalCharge": "0",
        "StartKms": "432588",
        "EndKms": "432606",
        "UseFixedRate": "1",
        "CalcHours": "0",
        "AC": "1",
        "DayRent": "1800.0",
        "ChargePerKm": "9.0",
        "MaximumKms": "0.0",
        "ExtraCharge": "9.0",
        "HourlyCharge": "150.0",
        "MinimumHours": "0.0",
        "HillCharge": "0.0",
        "PermitCharge": "0.0",
        "LocalExtraCharge": "9.0",
        "Rate": "505.0",
        "Distance": "60.0",
        "Duration": "4.0",
        "DurationPeriod": "Hours",
        "FoundJourney": "1",
        "Discount": "0.0",
        "PrintBiller": "1",
        "Biller": "PRADYUMNA",
        "Calc24HrDay": "1",
        "ServiceTax": "0.0",
        "ServiceTaxTotal": "0.0",
        "Owner": "KRISHNAN",
        "ChangedDaysTravelled": "0",
        "IncreaseDays": "1",
        "DriverFood": "0.0",
        "GST": "5.0",
        "IsOtherState": "0"
    },
 */

class ZohoApiService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $organizationId;
    protected string $zohoDomain;
    protected string $invoicePrefix;

    public function __construct()
    {
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
        $this->organizationId = config('services.zoho.organization_id');
        $this->zohoDomain = config('services.zoho.domain', 'https://www.zohoapis.in');
        $this->zohoInvoicePrefix = config('services.zoho.invoice_prefix');
    }

    public function getAccessToken(string $scope): string|null
    {
        $scopes = [
            'contacts'        => "ZohoBooks.contacts.READ,ZohoBooks.contacts.Create,ZohoBooks.contacts.UPDATE",
            'settings'        => "ZohoBooks.settings.READ,ZohoBooks.settings.Create,ZohoBooks.settings.UPDATE",
            'invoices'        => "ZohoBooks.invoices.READ,ZohoBooks.invoices.Create,ZohoBooks.invoices.UPDATE",
            'purchaseorders'  => "ZohoBooks.purchaseorders.READ,ZohoBooks.purchaseorders.Create,ZohoBooks.purchaseorders.UPDATE",
            'chartofaccounts' => "ZohoBooks.accountants.READ,ZohoBooks.accountants.Create,ZohoBooks.accountants.UPDATE",
        ];

        $requestedScope = $scopes[$scope] ?? null;

        if (!$requestedScope) {
            throw new \InvalidArgumentException("Invalid scope: $scope");
        }

        $cacheKey = 'zoho_token_' . md5($this->clientId . $requestedScope . $this->organizationId);

        $postData = [
            "grant_type"    => "client_credentials",
            "client_id"     => $this->clientId,
            "client_secret" => $this->clientSecret,
            "scope"         => $requestedScope,
            "soid"          => $this->organizationId
        ];
        
        return Cache::remember($cacheKey, 3600, function () use ($requestedScope) {
        //return Cache::remember($cacheKey, 3600, function () use ($requestedScope) {
            $response = Http::asForm()->post("https://accounts.zoho.in/oauth/v2/token", [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => $requestedScope,
                'soid'          => $this->organizationId,
            ]);

            if ($response->failed()) {
                Log::error('Zoho token error', ['response' => $response->json()]);
                return null;
            }

            Log::info('Requesting new Zoho access token...');
            
            return $response->json()['access_token'] ?? null;
        });
    }

    public function searchItemByName(string $name): array 
    {
        $accessToken = $this->getAccessToken('settings');

        if (!$accessToken) {
            return ['error' => 'Could not retrieve items access token'];
        }

        $searchUrl = "https://www.zohoapis.in/books/v3/items";
        $searchResponse = Http::withToken($accessToken)
            ->get($searchUrl, [
                'organization_id' => $this->organizationId,
                'name_startswith' => $name
            ]);

        if ($searchResponse->successful() && !empty($searchResponse['items'])) {
            foreach ($searchResponse["items"] as $row) {
                if ($row["name"] == $name) { // Exact match check
                    return ['success' => $row['item_id']];
                }
            }
        }

        return ['error' => 'Could not retrieve the Transport Charges item.'];
    }

    function getZohoInvoiceByReference($reference) {
        $accessToken = $this->getAccessToken('invoices');

        if (!$accessToken) {
            return ['error' => 'Could not retrieve invoices access token'];
        }

        $searchUrl = "https://www.zohoapis.in/books/v3/invoices";
        $searchResponse = Http::withToken($accessToken)
            ->get($searchUrl, [
                'organization_id'   => $this->organizationId,
                'reference_number'  => $reference
            ]);

        if ($searchResponse->successful() && !empty($searchResponse['invoices'])) {
            foreach ($searchResponse["invoices"] as $row) {
                if ($row["reference_number"] == $reference) { // Exact match check
                    return ['success' => $row['invoice_id']];
                }
            }
        }

        return ['error' => 'Invoice reference number not found'];
    }

    public function getOrCreateVendor(string $customerName): array
    {
        /*
        $customer = Customer::where('customer_name', $customerName)->first();

        if (!$customer) {
            return ['error' => 'Customer not found in local database.'];
        }

        if (!empty($customer->zoho_customer_id)) {
            return ['success' => $vendor->zoho_customer_id];
        }
        */
        $accessToken = $this->getAccessToken('contacts');

        if (!$accessToken) {
            return ['error' => 'Could not retrieve contacts access token'];
        }

        // Step 1: Search for the customer
        $searchUrl = "https://www.zohoapis.in/books/v3/contacts";
        $searchResponse = Http::withToken($accessToken)
            ->get($searchUrl, [
                'organization_id' => $this->organizationId,
                'contact_name_startswith' => $customerName
            ]);

        if ($searchResponse->successful() && !empty($searchResponse['contacts'])) {
            $zohoCustomerId = $searchResponse['contacts'][0]['contact_id'];
            //$vendor->zoho_vendor_id = $zohoVendorId;
            //$vendor->save();

            return ['success' => $zohoCustomerId];
        }

        // Step 2: Create the customer
        $data = Http::withToken($accessToken)
            ->post($searchUrl, [
                'organization_id' => $this->organizationId,
                'contact_name'    => $customerName,
                'vendor_name'     => $customerName,
                'contact_type'    => 'customer',
                //'gst_no'          => $vendor->gstin,
                'gst_treatment'   => 'consumer',
            ]);

        
        if (($data['code'] == 0) && (!empty($data['contact']))) {
            $zohoCustomerId = $data['contact']['contact_id'] ?? null;

            if ($zohoCustomerId) {
                //$vendor->zoho_vendor_id = $zohoVendorId;
                //$vendor->save();

                return ['success' => $zohoCustomerId];
            }
        }

        return ['error' => 'Failed to create vendor in Zoho.', 'details' => $createResponse->json()];
    }



    public function createInvoice(array $data): array
    {
        $accessToken = $this->getAccessToken('invoices');
        if (!$accessToken) {
            return ['error' => 'Could not retrieve invoices access token in createInvoice'];
        }

        $existing = $this->getInvoiceByReference($data['reference_number']);
        if ($existing) {
            return ['error' => 'Invoice '.$data['reference_number'].' already exists', 'invoice_id' => $existing];
        }

        $response = Http::withToken($accessToken)->post("https://www.zohoapis.in/books/v3/invoices", [
            'organization_id' => $this->organizationId,
        ] + $data);

        if (($response['code'] == 0) && (!empty($response['invoice']))) {
            return ['success' => $response['invoice']['invoice_id']];
        }
        else {
            Log::error('Zoho Invoice Create Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return ['error' => 'Error creating invoice'];
        }

        return ['error' => $response->json()];
    }

    public function getInvoiceByReference(string $reference): ?string
    {
        $accessToken = $this->getAccessToken('invoices');
        if (!$accessToken) {
            return ['error' => 'Could not retrieve invoices access token in getInvoiceByReference'];
        }

        $response = Http::withToken($accessToken)->get("https://www.zohoapis.in/books/v3/invoices", [
            'organization_id'   => $this->organizationId,
            'reference_number'  => $reference,
        ]);

        $json = $response->json();

        return $json['invoices'][0]['invoice_id'] ?? null;
    }

    public function createPurchaseOrder(array $data): array
    {
        $token = $this->getAccessToken('purchaseorders');

        $existing = $this->getPurchaseOrderByReference($data['reference_number']);
        if ($existing) {
            return ['success' => 'Purchase order already exists', 'purchase_order_id' => $existing];
        }

        $response = Http::withToken($token)->post("{$this->zohoDomain}/books/v3/purchaseorders", [
            'organization_id' => $this->organizationId,
        ] + $data);

        return $response->json();
    }

    public function getPurchaseOrderByReference(string $reference): ?string
    {
        $token = $this->getAccessToken('purchaseorders');

        $response = Http::withToken($token)->get("{$this->zohoDomain}/books/v3/purchaseorders", [
            'organization_id'   => $this->organizationId,
            'reference_number'  => $reference,
        ]);

        $json = $response->json();

        return $json['purchaseorders'][0]['purchaseorder_id'] ?? null;
    }

    public function getTaxes(): array
    {
        $token = $this->getAccessToken('settings');

        $response = Http::withToken($token)->get("{$this->zohoDomain}/books/v3/settings/taxes", [
            'organization_id' => $this->organizationId,
        ]);

        return $response->json()['taxes'] ?? [];
    }

    public function getTaxIdByPercentage(array $taxes, float $percentage): array
    {
        $result = ['intra' => null, 'interstate' => null];

        foreach ($taxes as $tax) {
            if ($tax['tax_percentage'] == $percentage) {
                if (Str::contains($tax['tax_name'], 'GST')) {
                    $result['intra'] = $tax['tax_id'];
                } elseif (Str::contains($tax['tax_name'], 'IGST')) {
                    $result['interstate'] = $tax['tax_id'];
                }
            }
        }

        return $result;
    }

    
}

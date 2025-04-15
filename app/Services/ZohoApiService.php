<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZohoApiService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $organizationId;
    protected string $zohoDomain;

    public function __construct()
    {
        $this->clientId = config('services.zoho.client_id');
        $this->clientSecret = config('services.zoho.client_secret');
        $this->organizationId = config('services.zoho.organization_id');
        $this->zohoDomain = config('services.zoho.domain', 'https://www.zohoapis.in');
    }

    public function getAccessToken(string $scope): string|null
    {
        $scopes = [
            'contacts'        => "ZohoBooks.contacts.READ,ZohoBooks.contacts.Create,ZohoBooks.contacts.UPDATE",
            'settings'        => "ZohoBooks.settings.READ,ZohoBooks.settings.Create,ZohoBooks.settings.UPDATE",
            'invoice'         => "ZohoBooks.invoices.READ,ZohoBooks.invoices.Create,ZohoBooks.invoices.UPDATE",
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
            $response = Http::asForm()->post("{$this->zohoDomain}/oauth/v2/token", [
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

    public function createInvoice(array $data): array
    {
        $token = $this->getAccessToken('invoice');

        $existing = $this->getInvoiceByReference($data['reference_number']);
        if ($existing) {
            return ['success' => 'Invoice already exists', 'invoice_id' => $existing];
        }

        $response = Http::withToken($token)->post("{$this->zohoDomain}/books/v3/invoices", [
            'organization_id' => $this->organizationId,
        ] + $data);

        return $response->json();
    }

    public function getInvoiceByReference(string $reference): ?string
    {
        $token = $this->getAccessToken('invoice');

        $response = Http::withToken($token)->get("{$this->zohoDomain}/books/v3/invoices", [
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

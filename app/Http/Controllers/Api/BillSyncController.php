<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillSyncController extends Controller
{
    public function sync(Request $request)
    {
        $providedToken = $request->header('X-Sync-Token');
        $expectedToken = config('app.sync_secret');
        $invoicePrefix = config('app.invoice_prefix');

        /*
        return response()->json([
            'provided' => $providedToken,
            'expected' => $expectedToken
        ]);
        */
        
        if (!$providedToken || $providedToken !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $bills = $request->all();

        if (!is_array($bills)) {
            return response()->json(['error' => 'Invalid payload format.'], 422);
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($bills as $data) {
            if (!isset($data['BillId'])) {
                $skipped++;
                continue;
            }

            // Upsert by BillId
            $bill = Bill::updateOrCreate(
                ['bill_id' => $data['BillId']],
                [
                    'bill_number'       => $invoicePrefix."-".$data['BillNumber'] ?? null,
                    'bill_date'         => $data['BillDate'] ?? null,
                    'customer'          => $data['Customer'] ?? null,
                    'from_place'        => $data['FromPlace'] ?? null,
                    'to_place'          => $data['ToPlace'] ?? null,
                    'dep_date'          => $data['DepDate'] ?? null,
                    'dep_time'          => $data['DepTime'] ?? null,
                    'arr_date'          => $data['ArrDate'] ?? null,
                    'arr_time'          => $data['ArrTime'] ?? null,
                    'amount'            => $data['Amount'] ?? null,
                    'vehicle_reg_no'    => $data['VehicleRegNo'] ?? null,
                    'driver_id'         => $data['DriverId'] ?? null,
                    'biller'            => $data['Biller'] ?? null,
                    'bill_type'         => $data['BillType'] ?? null,
                    'community'         => $data['Community'] ?? null,
                ]
            );

            $inserted++;
        }

        Log::info("Bill sync completed. Inserted: $inserted, Skipped: $skipped");

        return response()->json([
            'message'   => 'Sync completed',
            'inserted'  => $inserted,
            'skipped'   => $skipped,
            'status'    => 'success'
        ]);
        
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierController extends Controller
{
    public function searchDestination(Request $request)
    {
        $search = $request->query('search', '');
        if (empty($search)) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->timeout(10)->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'search' => $search,
                'limit' => 10,
                'offset' => 0
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }

            Log::warning('RajaOngkir Search Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('RajaOngkir Search Exception: ' . $e->getMessage());
        }

        return response()->json([
            ['id' => 17473, 'label' => 'Dummy Grogol, Jakarta Barat, DKI Jakarta, 11450'],
            ['id' => 17474, 'label' => 'Dummy Jelambar, Jakarta Barat, DKI Jakarta, 11460']
        ]);
    }

    public function getShippingCost(Request $request)
    {
        $request->validate([
            'origin' => 'required|numeric',
            'destination' => 'required|numeric',
            'weight' => 'required|numeric',
            'courier' => 'required|in:jne,pos,tiki'
        ]);

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->timeout(10)->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if ($response->successful()) {
                // Komerce returns an array of services directly in 'data'
                return response()->json($response->json()['data'] ?? []);
            }

            Log::warning('RajaOngkir Cost Calculation Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost Exception: ' . $e->getMessage());
        }

        // Dummy Data Fallback (matching Komerce format)
        return response()->json([
            [
                'code' => $request->courier,
                'name' => 'Dummy Courier',
                'service' => 'REG',
                'description' => 'Layanan Reguler Dummy',
                'cost' => 25000,
                'etd' => '2-3 Hari'
            ],
            [
                'code' => $request->courier,
                'name' => 'Dummy Courier',
                'service' => 'YES',
                'description' => 'Layanan Kilat Dummy',
                'cost' => 45000,
                'etd' => '1 Hari'
            ]
        ]);
    }
}

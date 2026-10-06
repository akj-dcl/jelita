<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                'key' => env('RAJAONGKIR_API_KEY')
            ])->timeout(3)->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'search' => $search,
                'limit' => 10,
                'offset' => 0
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Fallback for demo if API fails
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
                'key' => env('RAJAONGKIR_API_KEY')
            ])->timeout(5)->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if ($response->successful()) {
                // Komerce returns an array of services directly in 'data'
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Fallback
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

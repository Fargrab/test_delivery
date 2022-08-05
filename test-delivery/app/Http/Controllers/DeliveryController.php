<?php

namespace App\Http\Controllers;

use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function test(DeliveryService $deliveryService)
    {
        $elements = [
            [
                'weight' => 100,
                'width' => 100,
                'height' => 100,
                'deep' => 100,
                'count' => 1,
            ],
            [
                'weight' => 100,
                'width' => 100,
                'height' => 100,
                'deep' => 100
            ]
        ];

        dd($deliveryService->calculate('from', 'to', $elements));

    }
}

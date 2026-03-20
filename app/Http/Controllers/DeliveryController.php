<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryController extends Controller
{
    /**
     * Vista de remisión
     */
    public function show(Request $request)
    {
        $assetIds = $request->input('assets', []);

        $assets = Asset::whereIn('id', $assetIds)->get();

        return view('deliveries.remision', compact('assets'));
    }

    /**
     * Generar PDF
     */
    public function pdf(Request $request)
    {
        $assetIds = $request->input('assets', []);

        $assets = Asset::whereIn('id', $assetIds)->get();

        $pdf = Pdf::loadView('deliveries.remision', compact('assets'));

        return $pdf->download('remision_entrega.pdf');
    }
}
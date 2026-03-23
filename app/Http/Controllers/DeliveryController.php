<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AccessoryCheckout;
use App\Models\Asset;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeliveryController extends Controller
{

public function index()
{
    $deliveries = \App\Models\Delivery::latest()->limit(10)->get();

    return view('deliveries.index', compact('deliveries'));
}
    public function show(Request $request)
    {
        $assetIds = array_filter((array) $request->input('assets', []));
        $accessoryId = $request->input('accessory_id');

        $assets = collect();
        $primaryAsset = null;
        $relatedAssets = collect();
        $accessoryCheckoutsForAssignee = collect();
        $accessoryCheckoutsForAccessory = collect();

        if (count($assetIds) > 0) {
            $this->authorize('index', Asset::class);
            $assets = Asset::whereIn('id', $assetIds)->get();
            $primaryAsset = $assets->first();

            if ($primaryAsset && $primaryAsset->assigned_to) {
                $relatedAssets = Asset::where('assigned_to', $primaryAsset->assigned_to)
                    ->where('assigned_type', $primaryAsset->assigned_type)
                    ->whereNull('deleted_at')
                    ->whereNotIn('id', $assets->pluck('id'))
                    ->orderBy('asset_tag')
                    ->get();

                $accessoryCheckoutsForAssignee = AccessoryCheckout::query()
                    ->where('assigned_to', $primaryAsset->assigned_to)
                    ->where('assigned_type', $primaryAsset->assigned_type)
                    ->with('accessory')
                    ->orderBy('accessory_id')
                    ->get();
            }
        }

        if ($accessoryId) {
            $accessory = Accessory::findOrFail((int) $accessoryId);
            $this->authorize('view', $accessory);
            $accessoryCheckoutsForAccessory = AccessoryCheckout::query()
                ->where('accessory_id', $accessory->id)
                ->with(['accessory', 'assignedTo'])
                ->orderBy('assigned_to')
                ->get();
        }

        if ($assets->isEmpty() && ! $accessoryId) {
            abort(404, 'Indique al menos un equipo o un accesorio.');
        }

        $allAssetOptions = $assets->concat($relatedAssets)->unique('id')->values();
        $settings = Setting::getSettings();

        return view('deliveries.remision-form', compact(
            'assets',
            'primaryAsset',
            'relatedAssets',
            'allAssetOptions',
            'accessoryCheckoutsForAssignee',
            'accessoryCheckoutsForAccessory',
            'accessoryId',
            'settings'
        ));
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'modo' => 'nullable|in:simple,multiple',
            'observaciones' => 'nullable|string|max:5000',
            'assets' => 'nullable|array',
            'assets.*' => 'integer|exists:assets,id',
            'accessory_checkouts' => 'nullable|array',
            'accessory_checkouts.*' => 'integer|exists:accessories_checkout,id',
        ]);

        $modo = $request->input('modo', 'simple');
        $observaciones = $request->input('observaciones', '');

        $assetIds = array_map('intval', array_filter((array) $request->input('assets', [])));
        if ($modo === 'simple' && count($assetIds) > 1) {
            $assetIds = array_slice($assetIds, 0, 1);
        }

        $assets = count($assetIds) > 0
            ? Asset::whereIn('id', $assetIds)->get()
            : collect();

        $checkoutIds = array_map('intval', array_filter((array) $request->input('accessory_checkouts', [])));
        $accessoryRows = collect();
        if (count($checkoutIds) > 0) {
            $accessoryRows = AccessoryCheckout::whereIn('id', $checkoutIds)
                ->with('accessory')
                ->get();
        }

        if ($assets->isEmpty() && $accessoryRows->isEmpty()) {
            abort(422, 'Debe incluir al menos un equipo o herramienta.');
        }

        $settings = Setting::getSettings();
        $letterheadSrc = $this->letterheadDataUri($settings);

        // 🔥 Determinar destino
        $targetUserId = null;
        $targetLocationId = null;

        if ($assets->isNotEmpty()) {
            $first = $assets->first();
            $targetUserId = $first->assigned_to;
        }

        // 🔥 Crear Delivery
        $delivery = DB::transaction(function () use ($assets, $accessoryRows, $observaciones, $targetUserId, $targetLocationId) {

            $folio = 'REM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            $delivery = \App\Models\Delivery::create([
                'folio'       => $folio,
                'admin_id'    => auth()->id() ?? 1,
                'user_id'     => $targetUserId,
                'location_id' => $targetLocationId,
                'status'      => 'generada',
                'notes'       => $observaciones,
            ]);

            if ($assets->isNotEmpty()) {
                $delivery->assets()->attach($assets->pluck('id'));
            }

            if ($accessoryRows->isNotEmpty()) {
                $delivery->accessories()->attach(
                    $accessoryRows->pluck('accessory_id')->filter()
                );
            }

            return $delivery;
        });

        // 🔥 Generar PDF
        $pdf = Pdf::loadView('deliveries.remision', [
            'delivery'      => $delivery, // 🔥 IMPORTANTE
            'assets'        => $assets,
            'accessoryRows' => $accessoryRows,
            'observaciones' => $observaciones,
            'modo'          => $modo,
            'letterheadSrc' => $letterheadSrc,
            'settings'      => $settings,
        ])->setPaper('a4', 'portrait');

        // Guardar en storage/app/public/remisiones (disco storage_public).
        // El disk 'public' de Snipe-IT suele ser public/uploads, no storage/app/public.
        $disk = 'storage_public';
        if (! Storage::disk($disk)->exists('remisiones')) {
            Storage::disk($disk)->makeDirectory('remisiones');
        }

        $safeFolio = preg_replace('/[^A-Za-z0-9._-]+/', '_', $delivery->folio);
        $filename = 'remisiones/' . $safeFolio . '.pdf';

        Storage::disk($disk)->put($filename, $pdf->output());

        // 🔥 GUARDAR RUTA
        $delivery->update(['pdf_path' => $filename]);

        return $pdf->download($delivery->folio . '.pdf');
    }

    protected function letterheadDataUri(?Setting $settings): ?string
    {
        if (! $settings || empty($settings->remision_letterhead)) {
            return null;
        }

        $path = Storage::disk('public')->path($settings->remision_letterhead);
        if (! is_readable($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $data = base64_encode(file_get_contents($path));

        return "data:$mime;base64,$data";
    }

    public function download($id)
{
    $delivery = \App\Models\Delivery::findOrFail($id);

    $path = storage_path('app/public/' . $delivery->pdf_path);

    // 🔥 VERIFICAR SI EXISTE
    if (!file_exists($path)) {
        return "ERROR: No se encontró el archivo en: " . $path;
    }

    return response()->file($path);
}
public function showDelivery($id)
{
    $delivery = \App\Models\Delivery::with(['assets', 'accessories'])->findOrFail($id);

    return view('deliveries.show', compact('delivery'));
}
}
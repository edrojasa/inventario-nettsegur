<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AccessoryCheckout;
use App\Models\Asset;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * Formulario de remisión: observaciones, tipo simple/múltiple, equipos y herramientas.
     */
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

            if ($primaryAsset && $primaryAsset->assigned_to !== '' && $primaryAsset->assigned_to !== null) {
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
            abort(404, 'Indique al menos un equipo (assets) o un accesorio (accessory_id).');
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

    /**
     * Generar PDF de remisión (POST con observaciones y selección de ítems).
     */
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

        if ($assets->isNotEmpty()) {
            $this->authorize('index', Asset::class);
        }

        $checkoutIds = array_map('intval', array_filter((array) $request->input('accessory_checkouts', [])));
        $accessoryRows = collect();
        if (count($checkoutIds) > 0) {
            $accessoryRows = AccessoryCheckout::whereIn('id', $checkoutIds)
                ->with('accessory')
                ->get();
            foreach ($accessoryRows as $row) {
                if ($row->accessory) {
                    $this->authorize('view', $row->accessory);
                }
            }
        }

        if ($assets->isEmpty() && $accessoryRows->isEmpty()) {
            abort(422, 'Debe incluir al menos un equipo o una herramienta en la remisión.');
        }

        $settings = Setting::getSettings();
        $letterheadSrc = $this->letterheadDataUri($settings);

        $pdf = Pdf::loadView('deliveries.remision', [
            'assets' => $assets,
            'accessoryRows' => $accessoryRows,
            'observaciones' => $observaciones,
            'modo' => $modo,
            'letterheadSrc' => $letterheadSrc,
            'settings' => $settings,
        ])->setPaper('a4', 'portrait');

        $filename = 'remision_entrega_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Data URI para DomPDF (imagen membrete en settings).
     */
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
        $data = base64_encode((string) file_get_contents($path));

        return 'data:' . $mime . ';base64,' . $data;
    }
}

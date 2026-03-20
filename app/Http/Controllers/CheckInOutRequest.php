<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Location;
use App\Models\SnipeModel;
use App\Models\User;

trait CheckInOutRequest
{
    /**
     * Find target for checkout
     */
    protected function determineCheckoutTarget() : ?SnipeModel
    {
        switch (request('checkout_to_type')) {
            case 'location':
                return Location::findOrFail(request('assigned_location'));
            case 'asset':
                return Asset::findOrFail(request('assigned_asset'));
            default:
                return User::findOrFail(request('assigned_user'));
        }

        return null;
    }

    /**
     * Update the location of the asset passed in.
     */
    protected function updateAssetLocation($asset, $target) : Asset
    {
        switch (request('checkout_to_type')) {
            case 'location':
                $asset->location_id = $target->id;

                Asset::where('assigned_type', 'App\Models\Asset')
                    ->where('assigned_to', $asset->id)
                    ->update(['location_id' => $asset->location_id]);
                break;

            case 'asset':
                $asset->location_id = $target->rtd_location_id;

                if ($target->location_id != '') {
                    $asset->location_id = $target->location_id;
                }
                break;

            case 'user':
                $asset->location_id = $target->location_id;
                break;
        }

        return $asset;
    }

    /**
     * 🔥 NUEVO: Preparar datos para remisión / acta de entrega
     */
    protected function getDeliveryData($asset, $target) : array
    {
        return [
            'asset_name'        => $asset->name,
            'asset_tag'         => $asset->asset_tag,
            'serial'            => $asset->serial,

            'assigned_to'       => $target->name ?? 'N/A',
            'assigned_type'     => request('checkout_to_type'),

            'location'          => $asset->location_id,

            'delivered_by'      => auth()->user()->name ?? 'Sistema',
            'delivery_date'     => now()->format('Y-m-d H:i:s'),

            // Campos opcionales desde formulario
            'notes'             => request('notes'),
        ];
    }
}
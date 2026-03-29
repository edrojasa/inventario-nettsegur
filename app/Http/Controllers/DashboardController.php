<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Consumable;
use App\Models\Delivery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;

/**
 * Admin / operaciones: dashboard principal para cualquier usuario autenticado.
 */
class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        $counts = $this->buildDashboardCounts($user);
        $showInventoryOnboarding = $user->hasAccess('admin') && (int) ($counts['grand_total'] ?? 0) === 0;

        if ($user->hasAccess('admin')) {
            if ((! file_exists(storage_path().'/oauth-private.key')) || (! file_exists(storage_path().'/oauth-public.key'))) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('passport:install', ['--no-interaction' => true]);
            }
        }

        return view('dashboard', [
            'asset_stats' => null,
            'counts' => $counts,
            'showFullSnipeWidgets' => $user->hasAccess('admin'),
            'showActivityReportTable' => Gate::forUser($user)->allows('activity.view'),
            'showInventoryOnboarding' => $showInventoryOnboarding,
        ]);
    }

    /**
     * KPIs alineados con permisos de listado por módulo.
     */
    private function buildDashboardCounts(User $user): array
    {
        if ($user->hasAccess('admin')) {
            $counts = [
                'asset' => Asset::AssetsForShow()->count(),
                'license' => \App\Models\License::assetcount(),
                'accessory' => \App\Models\Accessory::count(),
                'consumable' => Consumable::count(),
                'component' => \App\Models\Component::count(),
                'user' => \App\Models\Company::scopeCompanyables($user)->count(),
            ];
            $counts['grand_total'] = $counts['asset'] + $counts['accessory'] + $counts['license'] + $counts['consumable'];
        } else {
            $counts = [
                'asset' => Gate::forUser($user)->allows('index', Asset::class)
                    ? Asset::AssetsForShow()->count()
                    : null,
                'license' => Gate::forUser($user)->allows('index', \App\Models\License::class)
                    ? \App\Models\License::assetcount()
                    : null,
                'accessory' => Gate::forUser($user)->allows('index', \App\Models\Accessory::class)
                    ? \App\Models\Accessory::count()
                    : null,
                'consumable' => Gate::forUser($user)->allows('index', Consumable::class)
                    ? Consumable::count()
                    : null,
                'component' => Gate::forUser($user)->allows('index', \App\Models\Component::class)
                    ? \App\Models\Component::count()
                    : null,
                'user' => Gate::forUser($user)->allows('index', User::class)
                    ? \App\Models\Company::scopeCompanyables($user)->count()
                    : null,
            ];
            $counts['grand_total'] = (int) collect([
                $counts['asset'],
                $counts['license'],
                $counts['accessory'],
                $counts['consumable'],
            ])->filter(static fn ($v) => $v !== null)->sum();
        }

        $counts['remisiones_7d'] = $this->countRemisionesLastSevenDays($user);

        return $counts;
    }

    /**
     * Remisiones registradas en los últimos 7 días (alcance global para admin; mismo criterio que el listado operativo para el resto).
     */
    private function countRemisionesLastSevenDays(User $user): int
    {
        $q = Delivery::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7));

        if (! $user->hasAccess('admin')) {
            $q->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('admin_id', $user->id);
            });
        }

        return (int) $q->count();
    }
}

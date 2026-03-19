<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Category;
use App\Services\InterCars\InterCarsClient;
use App\Services\InterCars\InterCarsProductSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class InterCarsController extends Controller
{
    /**
     * @param InterCarsClient             $client
     * @param InterCarsProductSyncService $syncService
     *
     * @return View
     */
    public function index(InterCarsClient $client, InterCarsProductSyncService $syncService): View
    {
        return view('back.catalog.intercars.index', [
            'categories'             => (new Category())->getList(false),
            'isConfigured'           => $client->configured(),
            'isCatalogLookupReady'   => $syncService->catalogLookupConfigured(),
            'catalogLookupSource'    => $syncService->catalogLookupSource(),
            'report'                 => session('intercars_sync_report'),
        ]);
    }


    /**
     * @param Request                     $request
     * @param InterCarsClient            $client
     * @param InterCarsProductSyncService $syncService
     *
     * @return RedirectResponse
     */
    public function sync(Request $request, InterCarsClient $client, InterCarsProductSyncService $syncService): RedirectResponse
    {
        $mode = (string) $request->input('mode', 'catalog_sync');

        if ($mode === 'stock_status_all') {
            $request->validate([
                'only_active' => 'nullable|boolean',
                'sync_status' => 'nullable|boolean',
            ]);
        } else {
            $request->validate([
                'category_id' => 'required|integer|exists:categories,id',
                'only_active' => 'nullable|boolean',
            ]);
        }

        if ( ! $client->configured()) {
            return redirect()->route('catalog.intercars.index')
                             ->withInput()
                             ->with(['error' => 'Inter Cars API nije konfiguriran. Dodajte INTERCARS_CLIENT_ID i INTERCARS_CLIENT_SECRET u .env.']);
        }

        $this->prepareLongRunningSync();

        try {
            if ($mode === 'stock_status_all') {
                $report = $syncService->syncStockStatusForExistingProducts([
                    'only_active' => $request->boolean('only_active', false),
                    'sync_status' => $request->boolean('sync_status', true),
                ]);
            } else {
                $report = $syncService->syncExistingProducts([
                    'category_id' => (int) $request->input('category_id'),
                    'only_active' => $request->boolean('only_active', true),
                ]);
            }
        } catch (Throwable $e) {
            return redirect()->route('catalog.intercars.index')
                             ->withInput()
                             ->with(['error' => $e->getMessage()]);
        }

        $redirect = redirect()->route('catalog.intercars.index')
                              ->withInput()
                              ->with('intercars_sync_report', $report);

        if ($report['total'] === 0) {
            return $redirect->with(['warning' => $mode === 'stock_status_all'
                ? 'U bazi nema lokalnih artikala s ispunjenim EAN-om ili SKU-om za sinkronizaciju količine i statusa.'
                : 'U odabranoj kategoriji nema lokalnih artikala s ispunjenim EAN-om ili SKU-om za sinkronizaciju.']);
        }

        if ($report['failed'] === $report['total'] && $report['total'] > 0) {
            return $redirect->with(['error' => $this->buildSummary($report)]);
        }

        if (($report['created'] + $report['updated']) === 0) {
            return $redirect->with(['warning' => $this->buildSummary($report)]);
        }

        return $redirect->with(['success' => $this->buildSummary($report)]);
    }


    /**
     * @param array $report
     *
     * @return string
     */
    private function buildSummary(array $report): string
    {
        $summary = sprintf(
            'Obrađeno: %d | Kreirano: %d | Ažurirano: %d | Preskočeno: %d | Greške: %d',
            $report['total'],
            $report['created'],
            $report['updated'],
            $report['skipped'],
            $report['failed']
        );

        if (($report['activated'] ?? 0) > 0 || ($report['deactivated'] ?? 0) > 0) {
            $summary .= sprintf(
                ' | Aktivirani: %d | Deaktivirani: %d',
                $report['activated'] ?? 0,
                $report['deactivated'] ?? 0
            );
        }

        return $summary;
    }


    /**
     * @return void
     */
    private function prepareLongRunningSync(): void
    {
        @ignore_user_abort(true);
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');
    }
}

<?php

namespace App\Console\Commands;

use App\Services\InterCars\InterCarsClient;
use App\Services\InterCars\InterCarsProductSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncInterCarsStockStatus extends Command
{
    /**
     * @var string
     */
    protected $signature = 'intercars:sync-stock-status
                            {--only-active : Obradi samo trenutno aktivne artikle}
                            {--without-status : Ažuriraj samo količinu, bez promjene statusa}
                            {--product-id=* : Ograniči obradu na odabrane product ID-jeve}';

    /**
     * @var string
     */
    protected $description = 'Sync Inter Cars quantity and status for existing local products.';


    /**
     * @param InterCarsClient             $client
     * @param InterCarsProductSyncService $syncService
     *
     * @return int
     */
    public function handle(InterCarsClient $client, InterCarsProductSyncService $syncService): int
    {
        if ( ! $client->configured()) {
            $message = 'Inter Cars API nije konfiguriran. Dodajte INTERCARS_CLIENT_ID i INTERCARS_CLIENT_SECRET u .env.';

            Log::warning('__Inter Cars stock/status sync skipped: ' . $message);
            $this->error($message);

            return 1;
        }

        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $startedAt = microtime(true);
        $options = [
            'only_active' => (bool) $this->option('only-active'),
            'sync_status' => ! (bool) $this->option('without-status'),
        ];

        $productIds = collect((array) $this->option('product-id'))
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter()
            ->values()
            ->all();

        if ( ! empty($productIds)) {
            $options['product_ids'] = $productIds;
        }

        Log::info('__Inter Cars stock/status sync started', $options);

        try {
            $report = $syncService->syncStockStatusForExistingProducts($options);
            $summary = $this->buildSummary($report, microtime(true) - $startedAt);

            Log::info('__Inter Cars stock/status sync finished: ' . $summary);
            $this->info($summary);

            return 0;
        } catch (Throwable $e) {
            Log::error('__Inter Cars stock/status sync failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $this->error($e->getMessage());

            return 1;
        }
    }


    /**
     * @param array $report
     * @param float $duration
     *
     * @return string
     */
    private function buildSummary(array $report, float $duration): string
    {
        $summary = sprintf(
            'Obrađeno: %d | Ažurirano: %d | Preskočeno: %d | Greške: %d | Trajanje: %.2f s',
            $report['total'] ?? 0,
            $report['updated'] ?? 0,
            $report['skipped'] ?? 0,
            $report['failed'] ?? 0,
            $duration
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
}

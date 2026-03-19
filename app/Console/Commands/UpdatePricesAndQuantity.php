<?php

namespace App\Console\Commands;

use App\Models\Back\Settings\Api\DataFeedWatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePricesAndQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prices_and_quantity
                            {--import-missing : Import missing products from feeds before updating existing ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices and quantities for products.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('__Update prices and quantities');

        $log_start = microtime(true);

        $report = (new DataFeedWatch())->syncProducts([
            'import_missing' => (bool) $this->option('import-missing'),
        ]);

        $log_end = microtime(true);
        Log::info('__DataFeedWatch report', $report);
        Log::info('__Update prices and quantities - Total Execution Time: ' . number_format(($log_end - $log_start), 2, ',', '.') . ' sec.');

        return 0;
    }
}

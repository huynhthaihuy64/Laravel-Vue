<?php

namespace App\Console\Commands;

use App\Http\Services\Product\ProductService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles data from resource API';

    /**
     * @var string $channel
     */
    protected string $channel;

    /**
     * @var ProductService $productService
     */
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $start = now();
        $startMsg = "[SYNC CURRENCY] Cron Job running at " . Carbon::now();
        $this->info($startMsg);
        Log::info('SYNC CURRENCY BEGIN');
        // Execute sync roles
        $this->info('[SYNC CURRENCY] Syncing currency...');
        $this->productService->updateListCurrency();

        $endMsg = vsprintf(
            "[SYNC CURRENCY] Cron Job end at %s, time elapsed: %s",
            [Carbon::now(), $start->diffForHumans(null, true)]
        );
        $this->info($endMsg);
        Log::info('SYNC CURRENCY END');

        return CommandAlias::SUCCESS;
    }
}

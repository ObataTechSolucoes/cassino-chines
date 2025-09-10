<?php

namespace App\Console\Commands;

use App\Services\AffiliateCommissionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessAffiliateCommissions extends Command
{
    protected $signature = 'affiliate:process {--from=} {--to=}';

    protected $description = 'Process affiliate commissions for a period';

    public function handle(AffiliateCommissionService $service): int
    {
        $from = $this->option('from') ? Carbon::parse($this->option('from')) : now()->startOfMonth();
        $to   = $this->option('to') ? Carbon::parse($this->option('to')) : now();
        $service->processPeriod($from, $to);
        $this->info('Affiliate commissions processed.');
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\PackageDiscount;
use Illuminate\Console\Command;

class DeleteExpiredPackageDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   // protected $signature = 'app:delete-expired-package-discounts';

    /**
     * The console command description.
     *
     * @var string
     */
   // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    protected $signature = 'discounts:cleanup';
    protected $description = 'Delete expired package discounts';

    public function handle()
    {
        $count = PackageDiscount::where('end_at', '<', now())->delete();
        $this->info("Deleted $count expired discounts.");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:cleanup-database')]
#[Description('Command description')]
class CleanupDatabase extends Command
{
    protected $signature = 'app:cleanup-database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedRows = DB::table('applications')
            ->where('created_at' , '<' , now()->subDays(30))
            ->delete();

        DB::statement('VACUUM');
    }
}

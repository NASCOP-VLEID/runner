<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VlFacility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:vl-facility {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile summary tables for viralload facilities';

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
     * @return mixed
     */
    public function handle()
    {
        //
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Eid;

class EidTat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:eid-tat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update eid tat on the sample table.';

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
        $eid = new Eid;

        $output = $eid->update_tat();

        $this->info($output);
    }
}
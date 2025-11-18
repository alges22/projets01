<?php

namespace App\Console\Commands;

use App\Services\External\AnipService;
use Illuminate\Console\Command;

class TestAnipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:anip {--npi=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get user information by NPI from ANIP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $npi = $this->option('npi');

        if(!$npi){
            $this->error("NPI is required ! ");

            return false;
        }else{
            $anipService = new AnipService();
            $result = $anipService->getPerson($npi);

            foreach ($result as $key => $value) {
                $this->info($key ." => ".$value);
            }

            return 1;

        }
    }
}

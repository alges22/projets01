<?php

namespace App\Console\Commands;

use App\Services\External\DGIService;
use Illuminate\Console\Command;

class TestDGICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dgi {--ifu=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get company information by IFU from DGI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ifu = $this->option('ifu');

        if(!$ifu){
            $this->error("IFU is required ! ");

            return false;
        }else{
            $dgiService = new DGIService();
            $result = $dgiService->getCompanyByIFU($ifu);

            if($result){
                foreach ($result as $key => $value) {
                    if(is_string($value)){
                        $this->info($key ." => ".$value);
                    }
                    if(is_array($value)){
                        foreach ($value as $key2 => $value2) {
                            $this->info($key2 ." => ".$value2);
                    }
                }
            }
        }

            return 1;
        }
    }
}

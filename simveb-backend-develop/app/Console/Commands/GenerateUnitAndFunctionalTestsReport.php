<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GenerateUnitAndFunctionalTestsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unit and functional tests report in a txt and a pdf format, respectively report.txt and report.pdf inside tests/reports directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        exec('mkdir -p tests/reports/');
        $command = [
            'bash',
            '-c',
            'php artisan test > tests/reports/report.txt ;'.
            'cupsfilter tests/reports/report.txt > tests/reports/report.pdf ;'.
            'cat tests/reports/report.txt'
        ];
        $process = new Process($command);
        $process->start();
        $totalTime = 50;
        $startTime = time();
        while ($process->isRunning()) {
            $elapsedTime = time() - $startTime;
            $progress = min(($elapsedTime / $totalTime) * 100, 100);
            $this->info(sprintf("Génération du rapport en cours: %.2f%%", $progress));
            sleep(2);
        }
        if ($process->isSuccessful()) {
            $this->info('Génération du rapport terminé !');
        } else {
            $this->error('Génération du rapport échoué : '.$process->getErrorOutput());
        }
        // exec('php artisan test > tests/reports/report.txt ; cupsfilter tests/reports/report.txt > tests/reports/report.pdf ; cat tests/reports/report.txt');
        return 0;
    }
}

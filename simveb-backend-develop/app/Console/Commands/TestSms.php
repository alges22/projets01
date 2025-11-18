<?php

namespace App\Console\Commands;

use App\Services\IdentityService;
use App\Services\SmsService;
use Illuminate\Console\Command;

class TestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sms {--phone=} {--npi=} {--message=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sms to a phone number.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $npi = $this->option('npi');
        $message = $this->option('message');

        if ((empty($phone) || empty($npi)) && empty($message)) {
            $this->error('Provide phone or npi and message.');

            return 0;
        }

        if ($npi) {
            $person = (new IdentityService)->showByNpi($npi)->response()->getData(true)['data'];

            $phone = $person['telephone'];
        }

        (new SmsService)->send($phone, $message);

        return 0;
    }
}

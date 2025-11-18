<?php

namespace App\Jobs;

use App\Services\WirepickService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $phone, private string $message) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch (config('config.sms.driver')) {
            case 'lafricamobile':
                break;
            case 'octopush':
                break;
            case 'ourvoice':
                break;
            case 'wirepick':
                (new WirepickService)->sendSms($this->phone, $this->message);
                break;
            case 'twilio':
                break;
            default:
                break;
        }
    }
}

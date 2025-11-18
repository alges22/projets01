<?php

namespace App\Console\Commands;

use App\Mail\BaseMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {email} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to a given address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $message = $this->argument('message');

        if (empty($email) || empty($message)) {
            $this->error('Provide mail and message.');

            return 0;
        }

        $emailPattern = "/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/";

        if (!preg_match($emailPattern, $email)) {
            $this->error('Provide a valid email address');

            return 0;
        }

        Mail::to($email)
            ->send(new BaseMail('Test mail', 'emails.base-mail', $message));

        return 0;
    }
}

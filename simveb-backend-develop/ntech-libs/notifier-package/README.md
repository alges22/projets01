# Nautilus Notifier

Managing notifications to multiple channels with Novu.

### 1 - Add dependencies

This package requires novu/novu-laravel to work then first of all run
`composer require novu/novu-laravel`.

### 2 - Setup

Run `php artisan vendor:publish` and select `Novu\Laravel\NovuServiceProvider` to publish novu config file.

Add this line to add your tenant identifier
` 'tenant_identifier' => env('NOVU_TENANT_IDENTIFIER'),`

Retrieve your API_key from Novu instance and past it in your env file.

### 3 - Config basic workflows
Notifier works with configured workflows from backoffice .

### 2- Use Notifier facade

Now you can use the Notifier facade to create subscribers and trigger notifications to channels (**in_app, email, sms, push, chat**).

**Example** :
    
    <?php

    use App\Consts\NotificationNames;
    use Illuminate\Support\Facades\Route;
    use Ntech\NotifierPackage\Enums\NotifierWorkflowTypeEnum;
    use Ntech\NotifierPackage\Facades\Notifier;

    Notifier::process(NotifierWorkflowTypeEnum::email->name, NotificationNames::OTP_VERIFICATION, ['112'], ['reference' => 'ref', 'link' => [
            'text' => 'Click',
            'url' => 'https://www.google.fr',
        ]]);

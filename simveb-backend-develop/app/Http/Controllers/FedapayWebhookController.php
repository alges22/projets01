<?php

namespace App\Http\Controllers;

use App\Services\FedapayService;
use App\Services\FedapayWebhookService;
use FedaPay\Webhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FedapayWebhookController extends Controller
{
    public function __construct(private readonly FedapayService $fedapayService, private readonly FedapayWebhookService $service)
    {}

    public function event()
    {
        $event = null;

        try {
            $event = Webhook::constructEvent(@file_get_contents('php://input'), $_SERVER['HTTP_X_FEDAPAY_SIGNATURE'], config('fedapay.webhook_secret_key'));
        } catch (\UnexpectedValueException $e) {
            Log::debug($e);
            http_response_code(400);
            exit();
        } catch (\FedaPay\Error\SignatureVerification $e) {
            Log::debug($e);
            http_response_code(400);
            exit();
        }

        $entity = $this->getFedapayOperationEntity($event);

        if (!$entity) {
            http_response_code(400);
            exit();
        }

        switch ($event->name) {
            case 'transaction.approved':
                $this->service->processTransactionApproved($entity);
                break;
            case 'transaction.canceled':
            case 'transaction.declined':
            case 'payout.sent':
            case 'payout.failed':
                break;
            default:
                http_response_code(400);
                exit();
        }

        http_response_code(200);
    }

    /**
     * Return new Fedapay transaction or Payout object.
     * @param $fedapayEvent
     * @return mixed
     */
    private function getFedapayOperationEntity($fedapayEvent)
    {
        if (Str::is(['transaction'], $fedapayEvent->object)) {
            return $this->fedapayService->retrieveTransaction(data_get($fedapayEvent, 'entity.id'));
        }

        return null;
    }
}

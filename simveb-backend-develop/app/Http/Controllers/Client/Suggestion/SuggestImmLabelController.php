<?php

namespace App\Http\Controllers\Client\Suggestion;

use App\Http\Controllers\Controller;
use App\Services\Immatriculation\SuggestionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SuggestImmLabelController extends Controller
{
    public function __construct(private readonly SuggestionService $service) {}

    public function checkLabelExist(Request $request)
    {
        $request->validate(['label' => ['required', 'string', 'max:8']]);

        $check = $this->service->checkLabelIsAvailable($request->label);

        return response($check, $check['available'] ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    public function checkNumberExist(Request $request)
    {
        $request->validate(['number' => ['required', 'numeric', 'digits:4']]);

        $check = $this->service->checkNumberIsAvailable($request->number);

        return response($check, $check['available'] ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    public function suggestNumbers(Request $request)
    {
        $request->validate(['template' => ['required', 'string', 'exists:number_templates,template']]);

        return response($this->service->suggestNumber($request->template));
    }
}

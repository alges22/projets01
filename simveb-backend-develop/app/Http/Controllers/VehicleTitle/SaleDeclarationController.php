<?php
namespace App\Http\Controllers\VehicleTitle;

use App\Http\Controllers\Controller;
use App\Services\Declaration\SaleDeclarationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaleDeclarationController extends Controller
{

    public function __construct(private readonly SaleDeclarationService $service)
    {
        //$this->authorizeResource(SaleDeclaration::class);
    }

    /**
     * Display the specified resource by reference.
     * @throws ValidationException
     */
    public function show(Request $request)
    {
        $requestData = ['reference' => $request->reference];
        Validator::make(
            $requestData,
            ['reference' => ['required', 'string', 'exists:sale_declarations,reference']]
        )->validate();

        [$success, $result] = $this->service->showByReference($requestData);

        return response($result, $success ? 200 : 404);
    }
}

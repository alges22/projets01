<?php
namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Ntech\UserPackage\Exceptions\CustomException;
use Ntech\UserPackage\Http\Requests\UpdatePasswordRequest;
use Ntech\UserPackage\Services\Auth\UpdatePasswordService;

/**
 * Class PasswordResetController
 * @package Ntech\UserPackage\Http\Controllers\Auth
 */
class PasswordExpiredResetController extends Controller
{

    public function __construct()
    {
        // parent::__construct();
        $this->middleware('set.language');
    }


    /**
     * Update Password
     * @param UpdatePasswordRequest $request
     * @param UpdatePasswordService $updatePasswordService
     * @return Application|ResponseFactory|Response
     * @throws CustomException
     */
    public function update(UpdatePasswordRequest $request,UpdatePasswordService $updatePasswordService)
    {
        $user = $updatePasswordService->getByEmail($request->email);
        $updatePasswordService->update($request->new_password, $user);
        return response("Mot de passe mis à jour avec succès",Response::HTTP_OK);
    }


}

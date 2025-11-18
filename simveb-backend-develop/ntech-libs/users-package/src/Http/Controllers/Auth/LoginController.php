<?php

namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Enums\ProfileTypesEnum;
use App\Http\Controllers\Controller;
use App\Models\Account\User;
use App\Models\Auth\ProfileType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use League\OAuth2\Server\AuthorizationServer;
use Ntech\UserPackage\Http\Requests\Login\SendOtpRequest;
use Ntech\UserPackage\Services\Auth\LoginService;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginController extends Controller
{
    use HandlesOAuthErrors;

    private $server;

    public function __construct(AuthorizationServer $server, private readonly LoginService $service)
    {
        $this->server = $server;
    }

    public function sendOtp(SendOtpRequest $request)
    {
        return $this->formatedResponse($this->service->sendOtp($request->validated()));
    }

    public function resendOtp(SendOtpRequest $request)
    {
        return $this->formatedResponse($this->service->resendOtp($request->validated()));
    }

    public function checkOtp(ServerRequestInterface $request)
    {
        $otpKey = request()->ip() . "-one-time-password";
        $otpCache = Cache::get($otpKey);

        if ($otpCache && Hash::check($request->getParsedBody()['password'], $otpCache['otp'])) {
            Cache::forget($otpKey);
            Cache::forget($request->getParsedBody()['username'] . '-user-data');

            $user = User::where('username', $request->getParsedBody()['username'])->first();

            if (!$user) {
                return $this->errorResponse('NPI invalide.', ResponseAlias::HTTP_NOT_FOUND);
            }

            $user->update(['password' => Hash::make($request->getParsedBody()['password'])]);

            $convertedResponse =  $this->convertResponse(
                $this->server->respondToAccessTokenRequest($request, new Psr7Response)
            );
            $content = json_decode($convertedResponse->getContent(), true);

            $userUpdateData = [
                'password' => null,
            ];
            if (empty($user->online_profile_id)) {
                $userUpdateData['online_profile_id'] = $user->profiles()->where('type_id', ProfileType::where('code', ProfileTypesEnum::user->name)->first()->id)->first()->id;
            }
            $user->update($userUpdateData);

            return $this->successResponse(array_merge(['message' => 'Code OTP valide.'], $content));
        } else {
            return $this->errorResponse('Code OTP invalide.', ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}

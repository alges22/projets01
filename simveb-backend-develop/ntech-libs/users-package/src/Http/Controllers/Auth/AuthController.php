<?php

namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Auth\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Ntech\UserPackage\Exceptions\UserNotFoundException;
use Ntech\UserPackage\Repositories\AuthRepository;
use Nyholm\Psr7\Response as Psr7Response;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Ntech\ActivityLogPackage\Services\ActivityLogService;
use Ntech\UserPackage\Services\Auth\AuthService;
use Ntech\UserPackage\Services\UserService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthController
 * @package Ntech\UserPackage\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    use HandlesOAuthErrors;

    /**
     * @var AuthorizationServer
     */
    private $server;
    /**
     * @var JwtParser
     */
    private $jwt;
    /**
     * @var TokenRepository
     */
    private $tokens;

    protected $activityLogService;

    public function __construct(
        AuthorizationServer $server,
        TokenRepository $tokens,
        JwtParser $jwt,
        ActivityLogService $activityLogService,
        private readonly AuthRepository $authRepository,
    ) {
        $this->jwt = $jwt;
        $this->server = $server;
        $this->tokens = $tokens;
        $this->activityLogService = $activityLogService;
        $this->middleware('auth:api')->except(['store', 'otpVerify', 'verifyEmail']);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke();
        $this->activityLogService->store(
            'Logout',
            $this->activityLogService::LOGOUT,
            'user',
            $request->user(),
            $request->user()
        );

        return response()->json([
            "success" => true,
            'message' => 'Vous avez été déconnecté avec succès!'
        ]);
    }


    /**
     * @param ServerRequestInterface $serverRequest
     * @return mixed
     * @throws UserNotFoundException
     */
    public function store(ServerRequestInterface $serverRequest)
    {
        $authService = new AuthService();
        $AttemptsResponse = $authService->proceedAttempt();

        if ($AttemptsResponse['status'] != Response::HTTP_OK) {
            return \response($AttemptsResponse, $AttemptsResponse['status']);
        }

        try {
            $convertedResponse =  $this->convertResponse(
                $this->server->respondToAccessTokenRequest($serverRequest, new Psr7Response)
            );
            $authService->resetAttempts(true);
            $content = json_decode($convertedResponse->getContent(), true);

            return  \response($content, Response::HTTP_OK);
        } catch (OAuthServerException $e) {
            $authService->logAttempt();
            return  \response([
                "error" => true,
                "message" => $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function verifyEmail(UserService $userService)
    {
        return $userService->verifyUser(request()->token);
    }

    public function currentUser()
    {
        return Auth::user() ? response($this->authRepository->retrieveUser(Auth::user())) : response([
            "success" => false,
            "message" => "Non authentifié",
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'string', new Password(6)],
        ]);

        auth()->user()->update([
            'password' => $request->password,
        ]);

        return response([
            'message' => 'Mot de passe mis à jour avec succès.',
            'code' => 200
        ]);
    }

    public function changeSpace(Request $request)
    {
        $request->validate([
            'profile_id' => ['required', 'exists:profiles,id']
        ]);

        $user = auth()->user();

        if (!in_array($request->profile_id, $user->profiles()->pluck('profiles.id')->toArray())) {
            return response([
                'error' => "Vous ne pouvez pas accéder à cette ressource.",
                'code' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }

        $profile = Profile::find($request->profile_id);
        if ($profile?->space?->status == Status::suspended->name) {
            return response([
                'error' => "L'espace auquel vous tentez d'accéder est suspendu.",
                'code' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update([
            'online_profile_id' => $request->profile_id,
        ]);

        return response([
            'message' => "Changement d'espace effectué avec succès.",
        ]);
    }

}

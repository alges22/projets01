<?php
namespace Ntech\UserPackage\Http\Controllers\Auth;

use App\Consts\NotificationNames;
use App\Http\Controllers\Controller;
use App\Models\Account\User;
use App\Notifications\NotificationSender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Ntech\UserPackage\Http\Controllers\ApiController;
use Ntech\UserPackage\Models\PasswordResetToken;
use Ntech\UserPackage\Notifications\PasswordResetRequest;
use Ntech\UserPackage\Notifications\PasswordResetSuccess;
use Ntech\UserPackage\Rules\IsValidPasswordRules;
use Ntech\UserPackage\Traits\ApiResponser;

/**
 * Class PasswordResetController
 * @package Ntech\UserPackage\Http\Controllers\Auth
 */
class PasswordResetController extends Controller {

    public function __construct()
    {
        //parent::__construct();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([ 'email' => 'required|string|email']);

        if (!$user = User::where('username', $request->email)->first()){

            return response([
                'message' => __('passwords.user'),
                'code' => 404
            ], 404);
        }

        $passwordReset = PasswordResetToken::updateOrCreate(['email' => $user->username], [
            'email' => $user->username,
            'token' => Str::random(80)
        ]);

        if ($user && $passwordReset){
            $url = config('config.backoffice_url') . '/forgot-password/' . $passwordReset->token;

            Notification::send($user,new NotificationSender(NotificationNames::PASSWORD_RESET_REQUEST, data: ['link' => ['url' => $url, 'text' => 'Réinitialiser']]));
        }

        return response()->json([
            'message' => __('passwords.sent'),
            'code' => 200
        ]);
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($token)
    {

        if (!$passwordReset = PasswordResetToken::where('token', $token)->first()){

            return $this->errorResponse( __('passwords.token'), 404);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->errorResponse( __('passwords.token'), 404);
        }

        return response()->json([
            'data' => $passwordReset,
            'code' => 200
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => ['required','confirmed', 'string', new Password(6)],
            'token' => 'required|string'
        ]);


        if (!$passwordReset = PasswordResetToken::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first()){

            return $this->errorResponse( __('passwords.token'), 404);
        }


        if (!$user = User::where('username', $passwordReset->email)->first()){


            return $this->errorResponse( __('passwords.token'), 404);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        $passwordReset->delete();

        Notification::send($user,new NotificationSender(NotificationNames::PASSWORD_RESET_SUCCESS));

        return response()->json([
            'user' => $user,
            'message' => 'Mot de passe réinitialisé avec succès.',
            'code' => 201
        ]);
    }


}

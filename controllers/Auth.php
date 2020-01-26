<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use JWTAuth;
use Validator;
use ValidationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use RainLab\User\Models\User as UserModel;
use Shohabbos\Shopaholicapi\Resources\UserResource;


class Auth extends Controller
{
    // Send code
    public function restorePassword() {
        $data = Input::only('email');

        $rules = [
            'email' => 'required|min:5|exists:users,email',
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()){
            return response()->json(['error' => "Введен не правильный логин"], 422);
        }

        $user = UserModel::where('email', $data['email'])->first();


        $user->reset_password_code = $code = rand(111111, 999999);
        $user->forceSave();

        $data = [
            'name' => $user->name,
            'code' => $code
        ];

        // send sms

        \Mail::send('rainlab.user::mail.restore', $data, function($message) use ($user) {
            $message->to($user->email, $user->full_name);
        });

        return [
            'success' => "Вам на почту было отправлено письмо с инструкцией для восстановления пароля".$code
        ];
    }

    // Confirm code & password
    public function resetPassword() {
        $data = Input::only('code', 'email', 'password');

        $rules = [
            'code' => 'required|min:4|max:8',
            'email' => 'required|exists:users,email',
            'password' => 'required|between:' . UserModel::getMinPasswordLength() . ',255'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()){
            return response()->json(['error' => $validation->messages()->first()], 422);
        }

        $user = UserModel::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['error' => "Пользователь не найден"], 422);
        }

        if (!$user->attemptResetPassword($data['code'], $data['password'])) {
            return response()->json(['error' => "Неизвестная ошибка"], 422);
        }

        return [
            'success' => 'Пароль изменен'
        ];
    }



    public function signin() {
        $data = Input::only('email', 'password');

        $rules = [
            'email' => 'required',
            'password' => 'required|min:5'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()){
            return response()->json([
                'error' => $validation->messages()->first()
            ], 422);
        }
        
        try {
            if (! $token = JWTAuth::attempt($data)) {
                return response()->json([
                    'error' => 'Login or password is wrong'
                ], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $userModel = JWTAuth::authenticate($token);
        $userModel = UserModel::find($userModel->id);


        return [
            'data' => new UserResource($userModel),
            'token' => $token,
            'success' => 'Добро пожаловать, '.$userModel->name
        ];
    }

    public function signup() {
        $credentials = Input::only('name', 'surname', 'email', 'email', 'password', 'password_confirmation', 'insurance_id');

        $rules = [
            'name'      => 'required|min:3',
            'surname'   => 'required|min:3',
            'email'  => 'required|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'email'     => 'required|min:3|email|unique:users,email'
        ];

        $validation = Validator::make($credentials, $rules);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->messages()->first()], 422);
        }
        
        try {
            $userModel = UserModel::create($credentials);

            $user = UserModel::find($userModel->id);
            $token = JWTAuth::fromUser($userModel);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return [
            'data' => new UserResource($user),
            'token' => $token,
            "success" => 'Регистрация успешно прошла'
        ];
    }

    
    public function refresh() {
        $user = null;
        $token = Input::get('token');

        try {
            // attempt to refresh the JWT
            if (!$token = JWTAuth::refresh($token)) {
                return response()->json(['error' => 'Cant refresh token'], 401);
            } else {
                $user = JWTAuth::authenticate($token);
            }


        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // if no errors are encountered we can return a new JWT
        return [
            'data' => $token,
            'user' => $user,
            "success" => 'Токен был обновлен'
        ];
    }

    public function invalidate() {
        $token = Input::get('token');
        
        try {
            // invalidate the token
            JWTAuth::invalidate($token);

        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_invalidate_token'], 500);
        }

        // if no errors we can return a message to indicate that the token was invalidated
        return response()->json([
            'success' => 'Токен удален'
        ]);
    }


}
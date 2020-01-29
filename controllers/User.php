<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Lang;
use Input;
use JWTAuth;
use Validator;
use ValidationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Models\UserGroup as UserGroupModel;
use Shohabbos\Shopaholicapi\Resources\UserResource;

class User extends Controller
{
    
    private $user;

    public function __construct() {
        $this->user = $this->auth();
    }

    public function orders() {
        $result = [];
        $data = $this->user->order()->with('status')->get();

        foreach ($data as $key => $value) {
            $item = $value->toArray();

            $item['total_price'] =  $value->total_price;
            $item['total_price_value'] =  $value->total_price_value;
            

            $result[] = $item;
        }

        return $result;
    }

    public function get() {
        return new UserResource($this->user);
    }

    public function setDeviceConf() {
        $user = $this->auth();

        $data = Input::only('device_type', 'device_id');
        if (!isset($data['device_type']) && empty($data['device_type'])) {
            $data['device_type'] = 'android';
        }

        $rules = [
            'device_type' => 'required|min:2|in:android,ios',
            'device_id' => 'required|min:5',
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()){
            return response()->json(['error' => $validation->messages()->first()], 422);
        }

        $user->device_id = $data['device_id'];
        $user->device_type = $data['device_type'];
        $user->save();

        return [
            'data'    => new UserResource($user),
            'success' => 'ok',
        ];
    }

    public function update() {
        $user = $this->auth();
        $data = Input::only('username', 'name', 'surname', 'password', 'user_address', 'password_confirmation', 'email', 'avatar', 'insurance_id');

        $rules = [
            'name' => 'string',
            'surname' => 'string',
            'username' => 'string',
            'user_address' => 'string',
            'email' => 'email',
            'avatar' => 'nullable|image',
            'password' => 'sometimes|required|between:6,255|confirmed',
            'password_confirmation' => 'required_with:password|between:6,255',
        ];
        

        $validation = Validator::make($data, $rules);
        if ($validation->fails()){
            return response()->json(['error' => $validation->messages()->first()], 422);
        }

        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }

        try {
            $user->fill($data);

            if (isset($data['avatar']) && $data['avatar']) {
                $user->avatar = $data['avatar'];
            }

            $user->save();
            $user = new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return [
            'data' => $user,
            'success' => "Данные успешно обновлены"
        ];
    }



    private function auth() {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $this->user = UserModel::find($user->id);
        } catch (\Exception $e) {

        }

        return $this->user;
    }

}
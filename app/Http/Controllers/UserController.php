<?php

namespace App\Http\Controllers;

use App\Exceptions\Dictionary;
use App\Modals\User;
use Illuminate\Http\Request;
use LogicException;

class UserController extends Controller
{

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        if (!User::attempt($email, $password)) {
            throw new LogicException(...Dictionary::LoginInfoError);
        }

        // 生成 token 的规则
        $user = User::getUserInfo($email, $password);
        $token_body = $user->id . ',' . (time() + 3600);
        $token_verify = md5($token_body . 'emotion_token');
        $token = base64_encode($token_body . ','. $token_verify);

        return $this->response(['token' => $token]);
    }

    public function logout()
    {

    }

    public function getList(Request $request)
    {
        $page = $request->input('page') ?? 1;
        $page_size = $request->input('page_size') ?? 5;

        return $this->response(User::getUserList($page_size, $page));
    }

    public function create(Request $request)
    {

        $this->validate($request, [
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $result = User::createUser($email, $password);

        if ($result == false) {
            throw new LogicException(...Dictionary::InternalError);
        }

        return $this->response(['id' => $result]);
    }

    public function update(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $id = $request->input('id') ?? '';

        if (empty($id)) {
            $result = User::createUser($email, $password);
        } else {
            $result = User::updateUser($id, $email, $password);
        }

        if ($result == false) {
            throw new LogicException(...Dictionary::InternalError);
        }

        return $this->response(['id' => empty($id) ? $result : $id]);
    }
}

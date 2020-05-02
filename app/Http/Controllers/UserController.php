<?php

namespace App\Http\Controllers;

use App\Exceptions\Dictionary;
use App\Modals\User;
use Illuminate\Http\Request;
use LogicException;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0",
 *      title="manage-api-service",
 *      description="管理平台后端服务",
 *      @OA\Contact(
 *          name="Rytia",
 *          email="admin@zzfly.net"
 *      )
 * )
 */
class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="用户登录",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "admin@zzfly.net", "password": "520"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *             type="object",
     *                  @OA\Property(
     *                       property="token",
     *                       type="string"
     *                   ),
     *                   example={"token": "MSwxNTg4NDAzNzQ5LGNjYjcwZDc0MDJjN2EzNGYyYjE0YmNjNDEzYTU4NzUz"}
     *         ),
     *     )
     * )
     */

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
        $token = base64_encode($token_body . ',' . $token_verify);

        return $this->response(['token' => $token]);
    }


    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="用户注销",
     *     description="后端无状态，前端取消 token 的使用即可",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string"
     *                 ),
     *                 example={"token": "MSwxNTg4NDAzNzQ5LGNjYjcwZDc0MDJjN2EzNGYyYjE0YmNjNDEzYTU4NzUz"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */

    public function logout()
    {

    }


    /**
     * @OA\Get(
     *     path="/v1/get_user_list",
     *     summary="获取用户列表",
     *     tags={"user"},
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="查询页码"
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="每页返回条数"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *             type="array",
     *              @OA\Items(
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"id": 1, "email": "admin@zzfly.net", "password": "603794e417b556ab8c4a169eec84fa88"}
     *             )
     *         ),
     *     )
     * )
     */

    public function getList(Request $request)
    {
        $page = $request->input('page') ?? 1;
        $page_size = $request->input('page_size') ?? 5;

        return $this->response(User::getUserList($page_size, $page));
    }


    /**
     * @OA\Post(
     *     path="/v1/create_user",
     *     summary="新增用户",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "admin@zzfly.net", "password": "520"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *             type="object",
     *                  @OA\Property(
     *                       property="id",
     *                       type="integer"
     *                   ),
     *                   example={"id": 9}
     *         ),
     *     )
     * )
     */

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


    /**
     * @OA\Post(
     *     path="/v1/update_user",
     *     summary="更新用户",
     *     description="若此接口不传入 id，则与 create_user 行为一致(即以传入的 email 和 password 新增用户)",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"id":9, "email": "admin@zzfly.net", "password": "520"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *             type="object",
     *                  @OA\Property(
     *                       property="id",
     *                       type="integer"
     *                   ),
     *                   example={"id": 9}
     *         ),
     *     )
     * )
     */

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

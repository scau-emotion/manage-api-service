<?php

namespace App\Http\Controllers;

use App\Exceptions\Dictionary;
use App\Modals\Template;
use Illuminate\Http\Request;
use LogicException;

class TemplateController extends Controller
{

    /**
     * @OA\Get(
     *     path="/v1/get_template_list",
     *     summary="获取模板列表",
     *     tags={"template"},
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
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 example={"id":"1","image":"http://www.baidu.com","description":"测试简介"}
     *             )
     *         ),
     *     )
     * )
     */

    public function getList(Request $request)
    {
        $page = $request->input('page') ?? 1;
        $page_size = $request->input('page_size') ?? 5;

        return $this->response(Template::getTemplateList($page_size, $page));
    }


    /**
     * @OA\Post(
     *     path="/v1/create_template",
     *     summary="新增模板",
     *     tags={"template"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"image", "description"},
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 example={"image":"http://www.baidu.com","description":"测试简介"}
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
            'image' => ['required'],
            'description' => ['required'],
        ]);

        $image = $request->input('image');
        $description = $request->input('description');
        $result = Template::createTemplate($image, $description);

        if ($result == false) {
            throw new LogicException(...Dictionary::InternalError);
        }

        return $this->response(['id' => $result]);
    }


    /**
     * @OA\Post(
     *     path="/v1/update_template",
     *     summary="更新模板",
     *     description="若此接口不传入 id，则与 create_template 行为一致(即以传入的信息新增模板)",
     *     tags={"template"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 example={"id":6, "image":"http://www.baidu.com","description":"测试简介"}
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

        $image = $request->input('image');
        $description = $request->input('description');
        $id = $request->input('id') ?? '';

        if (empty($id)) {
            $result = Template::createTemplate($image, $description);
        } else {
            $result = Template::updateTemplate($id, $image, $description);
        }

        if ($result == false) {
            throw new LogicException(...Dictionary::InternalError);
        }

        return $this->response(['id' => empty($id) ? $result : $id]);
    }
}

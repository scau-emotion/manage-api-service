<?php

namespace App\Http\Controllers;

use App\Exceptions\Dictionary;
use App\Modals\Template;
use Illuminate\Http\Request;
use LogicException;

class TemplateController extends Controller
{
    public function getList(Request $request)
    {
        $page = $request->input('page') ?? 1;
        $page_size = $request->input('page_size') ?? 5;

        return $this->response(Template::getTemplateList($page_size, $page));
    }

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

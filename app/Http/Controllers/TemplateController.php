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

    public function update(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');
        $id = $request->input('id') ?? '';

        if (empty($id)) {
            $result = Template::createTemplate($email, $password);
        } else {
            $result = Template::updateTemplate($id, $email, $password);
        }

        if ($result == false) {
            throw new LogicException(...Dictionary::InternalError);
        }

        return $this->response();
    }
}

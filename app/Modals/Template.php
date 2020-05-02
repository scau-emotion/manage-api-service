<?php

namespace App\Modals;

use App\Exceptions\Dictionary;
use App\Exceptions\LogicException;
use App\Exceptions\LoginInfoException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Template extends Model
{

    protected static $table_name = "templates";

    public static function getTemplateInfo($id)
    {
        $condition = [
            'id' => $id
        ];
        return DB::table(static::$table_name)->where($condition)->get()->first();
    }

    public static function getTemplateList($page_size = 5, $page = 1)
    {
        $offset = $page_size * ($page - 1);
        return DB::table(static::$table_name)->offset($offset)->limit($page_size)->get();
    }

    public static function createTemplate($image, $description)
    {
        // 参数非空校验
        if (empty($image) || empty($description)) {
            throw new LogicException(...Dictionary::CreateTemplateError);
        }

        $value = [
            'image' => $image,
            'description' => $description
        ];

        return DB::table(static::$table_name)->insertGetId($value);
    }

    public static function updateTemplate($id, $image = null, $description = null)
    {
        $condition = [
            'id' => $id
        ];

        // 组装需要修改的部分，非修改部分可以传空串或者 null
        $value = [];
        !empty($image) && $value['image'] = $image;
        !empty($description) && $value['description'] = $description;

        // 未作修改
        if (empty($value)) {
            return true;
        }

        if ($rows = DB::table(static::$table_name)->where($condition)->count() == 0) {
            throw new LogicException(...Dictionary::UpdateTemplateError);
        }

        return DB::table(static::$table_name)->where($condition)->update($value);
    }

}

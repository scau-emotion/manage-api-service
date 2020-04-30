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
        $value = [
            'image' => $image,
            'description' => $description
        ];

        return DB::table(static::$table_name)->insert($value);
    }

    public static function updateTemplate($image, $description)
    {
        $condition = [
            'id' => $id
        ];

        $value = [
            'image' => $image,
            'description' => $description
        ];

        if ($rows = DB::table(static::$table_name)->where($condition)->count() == 0) {
            throw new LogicException(...Dictionary::UpdateTemplateError);
        }

        return DB::table(static::$table_name)->where($condition)->update($value);
    }

}

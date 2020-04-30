<?php

namespace App\Modals;

use App\Exceptions\Dictionary;
use App\Exceptions\LogicException;
use App\Exceptions\LoginInfoException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected static $table_name = "users";
    protected static $password_salt = "emotion";

    public static function attempt($email, $password)
    {
        $condition = [
            'email' => $email,
            'password' => md5($password . 'emotion')
        ];
        $rows = DB::table(static::$table_name)->where($condition)->count();

        return $rows === 1;
    }

    public static function getUserInfo($email, $password)
    {
        $condition = [
            'email' => $email,
            'password' => md5($password . static::$password_salt)
        ];
        return DB::table(static::$table_name)->where($condition)->get()->first();
    }

    public static function getUserList($page_size = 5, $page = 1)
    {
        $offset = $page_size * ($page - 1);
        return DB::table(static::$table_name)->offset($offset)->limit($page_size)->get();
    }

    public static function createUser($email, $password)
    {
        $condition = [
            'email' => $email,
        ];

        $value = [
            'email' => $email,
            'password' => md5($password . static::$password_salt)
        ];

        if ($rows = DB::table(static::$table_name)->where($condition)->count() > 0) {
            throw new LogicException(...Dictionary::CreateUserError);
        }

        return DB::table(static::$table_name)->insert($value);
    }

    public static function updateUser($id, $email, $password)
    {
        $condition = [
            'id' => $id
        ];

        $value = [
            'email' => $email,
            'password' => md5($password . static::$password_salt)
        ];

        if ($rows = DB::table(static::$table_name)->where($condition)->count() == 0) {
            throw new LogicException(...Dictionary::UpdateUserError);
        }

        return DB::table(static::$table_name)->where($condition)->update($value);
    }

}

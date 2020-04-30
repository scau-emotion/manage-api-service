<?php
namespace App\Exceptions;

class Dictionary
{
    const InternalError = ['程序内部错误', '500500'];
    const LoginInfoError = ['登录信息错误', '500001'];
    const CreateUserError = ['创建失败，邮箱已被占用', '500002'];
    const UpdateUserError = ['更新失败，找不到用户', '500003'];
    const UpdateTemplateError = ['更新失败，找不到模板', '500004'];
}

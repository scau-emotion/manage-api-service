<?php
namespace App\Exceptions;

class Dictionary
{
    const InternalError = ['程序内部错误', '500500'];
    const LoginInfoError = ['登录信息错误', '500001'];
    const UserEmailExistError = ['邮箱已被占用', '500002'];
    const CreateUserInfoError = ['创建失败，用户信息有误', '500003'];
    const UserNotFoundError = ['操作失败，找不到用户', '500004'];
    const CreateTemplateError = ['创建失败，模板信息有误', '500005'];
    const TemplateNotFoundError = ['操作失败，找不到模板', '500006'];
}

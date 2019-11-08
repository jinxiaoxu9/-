<?php

namespace app\admin\validate;


use think\Validate;

class GroupValidate extends Validate
{
    protected $rule = [
        'title'     => 'require|min:1|max:32|unique:group',
        'menu_auth'   => 'require', //姓名

    ];
    protected $message = [
        'account.require'     => '角色名不能为空',
        'account.min'   => '角色名长度为6-32个字符',
        'account.max'   => '角色名长度为6-32个字符',
        'account.unique'   => '角色名已经存在',
        'menu_auth.require'     => '权限不能为空',
    ];
}
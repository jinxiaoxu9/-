<?php

namespace app\admin\validate;


use think\Validate;

class UserValidate extends Validate
{

    protected $rule = [
        'account'     => 'require|min:6|max:32|unique:user',
        'username'   => 'require', //姓名
        'login_pwd'        => 'require|min:6|max:32',
        'relogin_pwd' => 'require',
        'safety_pwd' => 'require|min:6|max:20', //验证交易密码
        'resafety_pwd' => 'require|confirm:safety_pwd', //验证交易密码
        'mobile' => 'require|mobile|unique:user', //手机号码
    ];
    protected $message = [
        //验证用户名
        'account.require'     => '账号不能为空',
        'account.min'   => '账号长度为6-32个字符',
        'account.max'   => '账号长度为6-32个字符',
        'account.unique'   => '账号已被使用',
        //验证姓名
        'username.require'  => '姓名不能为空',
        //验证登录密码
        'login_pwd.require' => '密码不能为空',
        'login_pwd.min' => '密码长度为6-20位',
        'login_pwd.max' => '密码长度为6-20位',
        //确认验证登录密码
        'relogin_pwd.require' => '确认密码不能为空',
        'relogin_pwd.confirm' => '两次输入的密码不一致',
        //验证交易密码
        'safety_pwd.require' => '交易密码不能为空',
        'safety_pwd.min' => '交易密码长度为6-20位',
        'safety_pwd.max' => '交易密码长度为6-20位',
        //确认验证交易密码
        'resafety_pwd.require' => '确认交易密码不能为空',
        'resafety_pwd.confirm' => '两次输入的交易密码不一致',
        //验证手机号码
        'mobile.mobile' => '手机号码不能为空',
        'mobile.mobile' => '手机号码格式不正确',
        'mobile.unique' => '手机号被占用',
    ];

    protected $scene = [

    ];
}
apiSelect["index"] = {
    login: {
        title: '用户登录',
        uri: '/index/user/login',
        type: 'post',
        dataType: 'json',
        params:{
            'account': 'username(用户名)',
            'password':'password(密码)',
        },
        comment:{
            "message": "登录成功",
            "status": 1,
            "result": {
                "token": "76833cbc701db1125681c97e6a28ebf9(用户唯一token标识)"
            }
        },

    },
    register: {
        title: '用户注册',
        uri: '/index/user/register',
        type: 'post',
        dataType: 'json',
        params:{
            'username': 'username(用户名)',
            'password':'password(密码)',
            'invent_code': 'invent_code(邀请码)',
            'mobile': 'mobile(手机号码)',
        },
        comment:{
            "message": "注册成功！",
            "status": 1
        }
    },
    userBasicInfo: {
        title: '用户基本信息',
        uri: '/index/user/userBasicInfo',
        type: 'post',
        dataType: 'json',
        // params:{
        //     'token': 'token(用户唯一标识)',
        // },
        comment:{
            "message": "success",
            "status": 1,
            "result": {
                "userid": 1752,
                "mobile": "13556609715",
                "money": 399,
                "work_status": 1,
                "status": 1,
                "username": "lijianyun",
                "wx_no": null,
                "activate": 0,
                "alipay": null,
                "truename": null,
                "email": "",
                "userqq": "0",
                "add_admin_id": 18,
                "group_id": 0,
                "token": "76833cbc701db1125681c97e6a28ebf9"
            }
        },

    },

};

runGroup("user");

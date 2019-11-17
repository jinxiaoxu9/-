apiSelect["index"] = {
    login: {
        title: '登录',
        uri: '/index/index/dologin',
        type: 'post',
        dataType: 'json',
        params:{
            'account': 'username(用户名)',
            'password':'password(密码)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },
    register: {
        title: '注册',
        uri: '/index/index/doregister',
        type: 'post',
        dataType: 'json',
        params:{
            'username': 'username(用户名)',
            'password':'password(密码)',
            're_password':'re_password(重复密码)',
            'invent_code': 'invent_code(邀请码)',
            'mobile': 'mobile(手机号码)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },

};

runGroup("index");

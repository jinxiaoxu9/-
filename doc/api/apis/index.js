apiSelect["index"] = {
    login: {
        title: '登录',
        uri: '/index/index/dologin',
        type: 'post',
        dataType: 'json',
        params:{
            'account': 'username',
            'password':'password',
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
            'username': 'username',
            'password':'password',
            'invent_code': 'invent_code',
            'mobile': 'mobile',
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

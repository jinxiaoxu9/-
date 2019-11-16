apiSelect["security"] = {
    getSecurityInfo: {
        title: '获取用户是否已经添加安全码',
        uri: '/index/security/getSecurityInfo',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            "message": "获取成功",
            "status": 1,
            "result": {
                "have_security": '0表示从未设置过,1表示已经设置过'
            }
        },
    },

    updateSecurityPassword: {
        title: '修改安全码',
        uri: '/index/security/updateSecurityPassword',
        type: 'post',
        dataType: 'json',
        params:{
            'old_security': 'old_security 原始安全码,如果getSecurityInfo返回have_security为0,这个输入框隐藏',
            'security': 'security 新安全码',
            're_security': 're_security 重复新安全码',
        },
        comment:{
            "message": "添加成功！",
            "status": 1
        },
    },

    updateLoginPassword: {
        title: '修改登录密码',
        uri: '/index/security/updateLoginPassword',
        type: 'post',
        dataType: 'json',
        params:{
            'old_password': 'old_password//老密码',
            'new_password': 're_new_password//新密码',
            're_new_password': 're_new_password//重复新密码',
        },
        comment:{
            "message": "操作成功！",
            "status": 1
        },
    },

};

runGroup("security");

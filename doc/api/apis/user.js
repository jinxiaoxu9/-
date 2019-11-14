apiSelect["user"] = {
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

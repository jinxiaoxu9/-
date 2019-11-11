apiSelect["order"] = {
    login: {
        title: '订单列表',
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
};

runGroup("order");

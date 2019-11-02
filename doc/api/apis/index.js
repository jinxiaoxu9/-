apiSelect["index"] = {
    login: {
        title: '登录',
        uri: '/Apis/game/gethotgame',
        type: 'post',
        dataType: 'json',
        params:{
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
        uri: '/Apis/game/gethotgame',
        type: 'post',
        dataType: 'json',
        params:{
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

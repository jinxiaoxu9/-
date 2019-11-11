apiSelect["home"] = {
    login: {
        title: '获取工作信息',
        uri: '/index/home/index',
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

    startWork: {
        title: '开工',
        uri: '/index/home/startWork',
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

    stopWork: {
        title: '停工',
        uri: '/index/home/stopWork',
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

runGroup("home");

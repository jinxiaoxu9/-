apiSelect["code"] = {
    codeTypes: {
        title: '二维码类型',
        uri: '/index/code/codeTypes',
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

    oderList: {
        title: '二维码列表',
        uri: '/index/code/codeList',
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

    addCode: {
        title: '添加二维码',
        uri: '/index/code/addCode',
        type: 'post',
        dataType: 'json',
        params:{
            'account_name': 'account_name',
            'type': 'type',
            'code_link':'code_link',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },

    delCode: {
        title: '删除二维码',
        uri: '/index/order/delCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },

    activeCode: {
        title: '激活二维码',
        uri: '/index/order/activeCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },

    disactiveCode: {
        title: '冻结二维码',
        uri: '/index/order/disactiveCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id',
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

runGroup("code");

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
            'account_name': 'account_name(账户号,用于识别)',
            'type': 'type(添加类型)',
            'code_link':'code_link(二维码链接，,当type=1,2的时候显示)',
            'banker_name':'banker_name(开户银行名称,当type=3的时候显示)',
            'bank_account_name':'bank_account_name(开户姓名,当type=3的时候显示)',
            'bank_account_number':'bank_account_number(开户卡号,当type=3的时候显示)',
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
            'code_id': 'code_id(二维码id)',
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
        uri: '/index/order/activeCode(二维码id)',
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
        uri: '/index/order/disactiveCode(二维码id)',
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

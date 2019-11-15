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
            'page': 'page(第几页)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:{
                "code_list":[
                    {
                        "id":"唯一id",
                        "status":"状态1表示已经激活，０表示未激活",
                        "type":"类型1表示微信,2表示支付宝，3表示银行卡",
                        "account_name" : "账户名称",
                        "continue_failed_num": "连续失败错数",
                        "bonus_points" : "提成,千分之几",
                        "order_today_all" :"今日收款次数",
                        "failed_order_num" :"失败次数(总和)",
                        "success_order_num" :"成功次数(总和)",
                        "qr_image":"二维码地址",
                        "create_time":"添加时间",
                        "account_number":"如果type=3是表示银行卡的详细信息，开户行，姓名，卡号用逗号分割",
                        'type_icon':"二维码类型图标"
                    },
                    {
                        "id":"唯一id",
                        "status":"状态1表示已经激活，０表示未激活",
                        "type":"类型1表示微信,2表示支付宝，3表示银行卡",
                        "account_name" : "账户名称",
                        "continue_failed_num": "连续失败错数",
                        "bonus_points" : "提成,千分之几",
                        "order_today_all" :"今日收款次数",
                        "failed_order_num" :"失败次数(总和)",
                        "success_order_num" :"成功次数(总和)",
                        "qr_image":"二维码地址",
                        "create_time":"添加时间",
                        "account_number":"如果type=3是表示银行卡的详细信息，开户行，姓名，卡号用逗号分割",
                        'type_icon':"二维码类型图标"
                    }
                ]
            }
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
            'security': 'security(安全码)',
            'image_link':'image_link(识别之后的二维码链接，,当type=1,2的时候显示)',
            'raw_image_link':'raw_image_link(原始未识别二维码链接，,当type=1,2的时候显示)',
            'banker_name':'banker_name(开户银行名称,当type=3的时候显示)',
            'bank_account_name':'bank_account_name(开户姓名,当type=3的时候显示)',
            'bank_account_number':'bank_account_number(开户卡号,当type=3的时候显示)',
        },
        comment:{
            "message": "添加成功",
            "status": 1
        },
    },

    delCode: {
        title: '删除二维码',
        uri: '/index/code/delCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id(二维码id)',
        },
        comment:{
            "message": "删除成功",
            "status": 1
        },
    },

    activeCode: {
        title: '激活二维码',
        uri: '/index/code/activeCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id(二维码id)',
        },
        comment:{
            "message": "激活成功",
            "status": 1
        },
    },

    disactiveCode: {
        title: '冻结二维码',
        uri: '/index/code/disactiveCode',
        type: 'post',
        dataType: 'json',
        params:{
            'code_id': 'code_id(二维码id)',
        },
        comment:{
            "message": "冻结成功",
            "status": 1
        },
    },
};

runGroup("code");

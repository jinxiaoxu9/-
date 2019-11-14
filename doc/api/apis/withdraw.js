apiSelect["withdraw"] = {
    add: {
        title: '提现申请',
        uri: '/index/Withdraw/add',
        type: 'post',
        dataType: 'json',
        params:{
            'bankcard_id': 'bankcard_id(用户银行卡主键ID)',
            'price':'price(提现金额)',
        },
        comment:{
            "message": "提现成功",
            "status": 1
        },

    },
    applyInfo: {
        title: '获取充值银行卡',
        uri: '/index/deposit/applyInfo',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            "message": "提现成功",
            "status": 1
        },
    },
    apply: {
        title: '充值申请',
        uri: '/index/deposit/apply',
        type: 'post',
        dataType: 'json',
        params:{
            'bank_name': 'bank_name(银行卡开户行)',
            'bank_account': 'bank_account(银行卡开户姓名)',
            'bank_number': 'bank_number(银行卡开户卡号)',
            'money': 'money(充值金额)',
            'name': 'name(充值姓名)',
        },
        comment:{
            "message": "申请成功",
            "status": 1
        },
    },
    depositList: {
        title: '获取充值记录',
        uri: '/index/deposit/depositList',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            "message": "成功",
            "status": 1,
            "result": {
                "deposit_list": {
                    "total": 2,
                    "per_page": 10,
                    "current_page": 1,
                    "data": [
                        {
                            "id": 3,
                            "uid": 5,
                            "account": "",
                            "name": "1",
                            "price": 1,
                            "way": 0,
                            "addtime": "1573734918",
                            "status": 0,
                            "marker": "",
                            "account_name": "1",
                            "account_num": "1",
                            "bank_name": "1"
                        },
                        {
                            "id": 2,
                            "uid": 5,
                            "account": "",
                            "name": "1",
                            "price": 1,
                            "way": 0,
                            "addtime": "1573734779",
                            "status": 0,
                            "marker": "",
                            "account_name": "1",
                            "account_num": "1",
                            "bank_name": "1"
                        }
                    ]
                }
            }
        },
    },
};

runGroup("withdraw");

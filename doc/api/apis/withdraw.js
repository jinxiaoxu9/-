apiSelect["withdraw"] = {
    bankindex: {
        title: '获取用户提现银行卡',
        uri: '/index/BankCard/index',
        type: 'post',
        dataType: 'json',
        comment:{
            "message": "success",
            "status": 1,
            "result": {
                "total": 3,
                "per_page": 15,
                "current_page": 1,
                "data": [
                    {
                        "id": 47,
                        "uid": 1752,
                        "name": "xxx芸",
                        "bankname": "传达银行",
                        "banknum": "848409831377333",
                        "addtime": "1573544295"
                    },
                    {
                        "id": 48,
                        "uid": 1752,
                        "name": "李xx",
                        "bankname": "中国农业银行",
                        "banknum": "62284840924588",
                        "addtime": "1573544370"
                    },
                    {
                        "id": 49,
                        "uid": 1752,
                        "name": "李xx",
                        "bankname": "中国信业银行",
                        "banknum": "62284840512477",
                        "addtime": "1573544387"
                    }
                ]
            }
        }
    },
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
    withdrawList: {
        title: '获取提现记录',
        uri: '/index/Withdraw/withdrawList',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            "message": "成功",
            "status": 1,
            "result": {
                "withdraw_list": {
                    "total": 2,
                    "per_page": 10,
                    "current_page": 1,
                    "data": [
                        {
                            "id": 10,
                            "uid": 6,
                            "account": "622848123456789258369",
                            "name": "小三子",
                            "way": "中国银行",
                            "price": 100,
                            "addtime": "1573923711",
                            "endtime": "1573923257处理时间",
                            "status": "0处理中2已驳回3已完成"
                        }
                    ]
                }
            }
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
            "message": "成功",
            "status": 1,
            "result": {
                "info": {
                    "account_name": "充值银行卡开户姓名",
                    "account_num": "充值银行卡卡号",
                    "bank_name": "充值银行卡开户行"
                }
            }
        },
    },
    apply: {
        title: '充值申请',
        uri: '/index/deposit/apply',
        type: 'post',
        dataType: 'json',
        params:{
            'bank_name': 'bank_name(银行卡开户行,applyInfo接口返回)',
            'bank_account': 'bank_account(银行卡开户姓名,applyInfo接口返回)',
            'bank_number': 'bank_number(银行卡开户卡号,applyInfo接口返回)',
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
                            "name": "test 充值姓名",
                            "price": "1",
                            "addtime": "1573734918",
                            "status": "0处理中2已驳回1已完成",
                            "marker": "note",
                            "account_name": "充值银行卡开户姓名",
                            "account_num": "充值银行卡开户卡号",
                            "bank_name": "充值银行卡开户行",
                            "deal_time": "处理时间"
                        },
                    ]
                }
            }
        },
    },
};

runGroup("withdraw");

apiSelect["BankCard"] = {
    add: {
        title: '添加银行卡',
        uri: '/index/BankCard/add',
        type: 'post',
        dataType: 'json',
        params:{
            'name': 'name(银行卡号账户名)',
            'bankname':'bankname(银行名称)',
            'banknum':'banknum(银行卡卡号)',
            'security':'security(安全码)',
        },
        comment:{
            "message": "添加成功",
            "status": 1,
        },

    },
    delBank: {
        title: '删除银行卡',
        uri: '/index/BankCard/delBank',
        type: 'post',
        dataType: 'json',
        params:{
            'bank_id': 'bank_id(银行卡id)'
        },
        comment:{
            "message": "删除成功",
            "status": 1,
        },
    },
    bankindex: {
        title: '用户银行卡列表',
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
                        "name": "xxx",
                        "bankname": "传达银行",
                        "banknum": "848409831377333",
                        "addtime": "1573544295"
                    },
                    {
                        "id": 48,
                        "uid": 1752,
                        "name": "xxx",
                        "bankname": "中国农业银行",
                        "banknum": "62284840924588",
                        "addtime": "1573544370"
                    },
                    {
                        "id": 49,
                        "uid": 1752,
                        "name": "xxxx",
                        "bankname": "中国信业银行",
                        "banknum": "62284840512477",
                        "addtime": "1573544387"
                    }
                ]
            }
        }
    },

};

runGroup("BankCard");

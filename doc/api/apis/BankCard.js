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
        },
        comment:{
            "message": "登录成功",
            "status": 1,
            "result":{
                "message": "银行卡添加成功",
                "status": 1
            }
        },

    },
    register: {
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
                        "name": "李健芸",
                        "bankname": "传达银行",
                        "banknum": "6228484098313873077333",
                        "addtime": "1573544295"
                    },
                    {
                        "id": 48,
                        "uid": 1752,
                        "name": "李健芸",
                        "bankname": "中国农业银行",
                        "banknum": "622848409522124588",
                        "addtime": "1573544370"
                    },
                    {
                        "id": 49,
                        "uid": 1752,
                        "name": "李健芸",
                        "bankname": "中国信业银行",
                        "banknum": "6228484098324512477",
                        "addtime": "1573544387"
                    }
                ]
            }
        }
    },

};

runGroup("BankCard");

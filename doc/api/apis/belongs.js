apiSelect["belongs"] = {
    index: {
        title: '个人中心用户资产数据(页面级API)',
        uri: '/index/Belongs/index',
        type: 'post',
        dataType: 'json',
        comment:{
            "message": "success",
            "status": 1,
            "result": {
                "money": "399.00",
                "codeTypeJjPercent": {
                    "1": {
                        "id": 1,
                        "type_name": "微信",
                        "type_logo": "./Uploads/2019-10-06/5d994cc4cf16b.jpg",
                        "sort": 1,
                        "create_time": "2019-10-06 10:01:17",
                        "limit_money": "15000.00",
                        "defualt_bonus_points": 0
                    },
                    "2": {
                        "id": 2,
                        "type_name": "支付宝",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg",
                        "sort": 2,
                        "create_time": "2019-10-06 10:02:09",
                        "limit_money": "17000.00",
                        "defualt_bonus_points": 0
                    },
                    "3": {
                        "id": 3,
                        "type_name": "支付宝转银行卡",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg",
                        "sort": 3,
                        "create_time": "2019-10-06 10:02:37",
                        "limit_money": "10000.00",
                        "defualt_bonus_points": 0
                    },
                    "4": {
                        "id": 4,
                        "type_name": "百付通",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg",
                        "sort": 4,
                        "create_time": "2019-10-06 10:02:37",
                        "limit_money": "5000.00",
                        "defualt_bonus_points": 0
                    }
                },
                "jjMoneys": "0.00",
                "skMoneys ": "0.00",
                "unskMoneys  ": "0.00"
            }
        },

    },
    changeLog: {
        title: '资产变动记录',
        uri: '/index/user/changeLog',
        type: 'post',
        dataType: 'json',
        comment:{
            "message": "success",
            "status": 1,
            "result": {
                "total": 23,
                "per_page": 10,
                "current_page": 1,
                "data": [
                    {
                        "id": 3400,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "关闭订单：11092",
                        "addtime": "1572270855",
                        "jc_class": "+",
                        "num": 400,
                        "pre_amount": "9110.00",
                        "last_amount": "9510.00"
                    },
                    {
                        "id": 3394,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "关闭订单：11088",
                        "addtime": "1572270574",
                        "jc_class": "+",
                        "num": 1000,
                        "pre_amount": "8110.00",
                        "last_amount": "9110.00"
                    },
                    {
                        "id": 3392,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "抢单成功,扣除余额",
                        "addtime": "1572270509",
                        "jc_class": "-",
                        "num": 500,
                        "pre_amount": "8610.00",
                        "last_amount": "8110.00"
                    },
                    {
                        "id": 3389,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "抢单成功,扣除余额",
                        "addtime": "1572270252",
                        "jc_class": "-",
                        "num": 400,
                        "pre_amount": "9010.00",
                        "last_amount": "8610.00"
                    },
                    {
                        "id": 3383,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "抢单成功,扣除余额",
                        "addtime": "1572269970",
                        "jc_class": "-",
                        "num": 1000,
                        "pre_amount": "10010.00",
                        "last_amount": "9010.00"
                    },
                    {
                        "id": 3316,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "关闭订单：11042",
                        "addtime": "1572268171",
                        "jc_class": "+",
                        "num": 500,
                        "pre_amount": "9510.00",
                        "last_amount": "10010.00"
                    },
                    {
                        "id": 3294,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "确认返还,添加余额",
                        "addtime": "1572267759",
                        "jc_class": "+",
                        "num": 1900,
                        "pre_amount": "7610.00",
                        "last_amount": "9510.00"
                    },
                    {
                        "id": 3288,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "抢单成功,扣除余额",
                        "addtime": "1572267566",
                        "jc_class": "-",
                        "num": 500,
                        "pre_amount": "8110.00",
                        "last_amount": "7610.00"
                    },
                    {
                        "id": 3271,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "抢单成功,扣除余额",
                        "addtime": "1572266774",
                        "jc_class": "-",
                        "num": 1900,
                        "pre_amount": "10010.00",
                        "last_amount": "8110.00"
                    },
                    {
                        "id": 2271,
                        "uid": 130,
                        "jl_class": 7,
                        "info": "关闭订单：10632",
                        "addtime": "1572234479",
                        "jc_class": "+",
                        "num": 500,
                        "pre_amount": "9510.00",
                        "last_amount": "10010.00"
                    }
                ]
            }
        }
    },


};

runGroup("belongs");

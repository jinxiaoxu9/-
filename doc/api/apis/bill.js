apiSelect["bill"] = {
    getBillTypes: {
        title: '资产变动类型',
        uri: '/index/Bill/getBillTypes',
        type: 'post',
        dataType: 'json',
        comment: {
            message: "success",
                "status": 1,
                "result": {
                "type_list": {
                    "1": "充值",
                        "2": "提现",
                        "3": "抢单成功,押金",
                        "4": "关闭订单,押金返回",
                        "5": "订单完成,添加利润",
                        "6": "后台强制完成订单"
                }
            }
        }
    },

    changeLog: {
        title: '帐变列表',
        uri: '/index/Bill/changeLog',
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

runGroup("bill");

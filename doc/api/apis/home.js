apiSelect["home"] = {
    index: {
        title: '获取工作信息',
        uri: '/index/home/index',
        type: 'post',
        dataType: 'json',
        params: {},
        comment: {
            status: 1,
            msg: "消息",
            url: "",
            result: {
                "info": {
                    "code_infos": [
                        {
                            "id": 1,
                            "type_name": "微信",
                            "rate": "20//费率"
                        },
                        {
                            "id": 2,
                            "type_name": "支付宝",
                            "rate": "20//费率"
                        },
                        {
                            "id": 3,
                            "type_name": "支付宝转银行卡",
                            "rate": "20//费率"
                        },
                        {
                            "id": 4,
                            "type_name": "百付通",
                            "rate": "20//费率"
                        }
                    ],
                    "work_status": '1//工作状态',
                    "money": '300.00用户余额',
                    "today_finish_money": '3000.00//今日完成订单总额',
                    "today_bonus": '200.00//今日订单分红',
                    "today_finish_order": '20//今日完成订单数量',
                    "today_total_order": '100//今日总订单数量',
                    "unread": '3//未读消息',
                    "queen_num": '3//当前排队第几位',
                    "today_success_rate": "10.00%今日成功率"
                }
            }
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

    queen: {
    title: '获取订单排队信息以及最新10条订单信息,前端每10s请求一次(此接口后面会修改暂时这样)',
        uri: '/index/home/stopWork',
        type: 'post',
        dataType: 'json',
        params:{
    },
    comment:{
        "message": "success",
        "status": 1,
        "result": {
            "qeeen【排队等待订单的用户数组--后续会修改】": [
                {
                    "user_id【会员ID】": 1,
                    "order_status【订单状态】": 1,
                    "add_time【订单匹配时间】": 1574134704
                },
                {
                    "user_id": 2,
                    "order_status": 1,
                    "add_time": 1574134704
                },
                {
                    "user_id": 5,
                    "order_status": 2,
                    "add_time": 1574134704
                },
                {
                    "user_id": 8,
                    "order_status": 2,
                    "add_time": 1574134704
                },
                {
                    "user_id": 3,
                    "order_status": 2,
                    "add_time": 1574134704
                },
                {
                    "user_id": 7,
                    "order_status": 2,
                    "add_time": 1574134704
                },
                {
                    "user_id": 5,
                    "order_status": 1,
                    "add_time": 1574134704
                },
                {
                    "user_id": 8,
                    "order_status": 1,
                    "add_time": 1574134704
                },
                {
                    "user_id": 5,
                    "order_status": 0,
                    "add_time": 1574134704
                }
            ],
            "userLastOrderId【当前用户最近的一条已支付的订单】": 903,
            "lastNewOrderLists【系统最近十条已支付的订单数据--响应字段可能会变,不清楚前端UI上需要哪些】": [
                {
                    "id": 903,
                    "order_no": "11094",
                    "gema_userid": 647
                },
                {
                    "id": 902,
                    "order_no": "11093",
                    "gema_userid": 130
                },
                {
                    "id": 892,
                    "order_no": "11083",
                    "gema_userid": 876
                },
                {
                    "id": 890,
                    "order_no": "11081",
                    "gema_userid": 1179
                },
                {
                    "id": 889,
                    "order_no": "11080",
                    "gema_userid": 1179
                },
                {
                    "id": 887,
                    "order_no": "11078",
                    "gema_userid": 786
                },
                {
                    "id": 885,
                    "order_no": "11076",
                    "gema_userid": 647
                },
                {
                    "id": 884,
                    "order_no": "11075",
                    "gema_userid": 594
                },
                {
                    "id": 883,
                    "order_no": "11074",
                    "gema_userid": 876
                },
                {
                    "id": 882,
                    "order_no": "11073",
                    "gema_userid": 636
                }
            ],
            "unreadMessage【用户未读取消息的数量】": 1
        }
    },
},
};

runGroup("home");

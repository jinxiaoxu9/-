apiSelect["home"] = {
    login: {
        title: '获取工作信息',
        uri: '/index/home/index',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            status : 1,
            msg : "消息",
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
                    "work_status": '工作状态',
                    "money": '用户余额',
                    "today_finish_money": '今日完成订单总额',
                    "today_bonus": '今日订单分红',
                    "today_finish_order": '今日完成订单数量',
                    "today_total_order": '今日总订单数量',
                    "unread": '未读消息',
                    "today_success_rate": "10.00%今日成功率"
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

};

runGroup("home");

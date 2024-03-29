apiSelect["order"] = {
    oderList: {
        title: '订单列表',
        uri: '/index/order/oderList',
        type: 'post',
        dataType: 'json',
        params:{
            'page': 'page(第几页)',
            'status': '0:表示支付中 1:表示成功完成订单 2:关闭状态的订单, -1: 所有订单',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },
    sureSk: {
        title: '确认收款',
        uri: '/index/order/sureSk',
        type: 'post',
        dataType: 'json',
        params:{
            'order_id': 'order_id(订单id)',
            'security':'security(安全码)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },

    uplaodCredentials: {
        title: '上传转款凭证',
        uri: '/index/order/uplaodCredentials',
        type: 'post',
        dataType: 'json',
        params:{
            'order_id': 'order_id(订单id)',
            'credentials':'credentials(凭证图片的路径)',
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

runGroup("order");

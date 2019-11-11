apiSelect["order"] = {
    login: {
        title: '订单列表',
        uri: '/index/order/oderList',
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

runGroup("order");

apiSelect["withdraw"] = {
    login: {
        title: '发起提现',
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
};

runGroup("withdraw");

apiSelect["share"] = {
    getInitShareInfo: {
        title: '获取添加分享链接初始化信息',
        uri: '/index/share/getInitShareInfo',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result: {
                "init_info": [
                    {
                        "id": 1,
                        "type_name": "微信//类型名称",
                        "type_logo": "./Uploads/2019-10-06/5d994cc4cf16b.jpg//类型图片地址",
                        "max": "20//能设置的最大值"
                    },
                    {
                        "id": 2,
                        "type_name": "支付宝//类型名称",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg//类型图片地址",
                        "max": "20//能设置的最大值"
                    },
                    {
                        "id": 3,
                        "type_name": "支付宝转银行卡//类型名称",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg//类型图片地址",
                        "max": "20//能设置的最大值"
                    },
                    {
                        "id": 4,
                        "type_name": "百付通//类型名称",
                        "type_logo": "./Uploads/2019-10-06/5d994aede29a5.jpg//类型图片地址",
                        "max": "20//能设置的最大值"
                    }
                ]
            }
        },
    },

    linkList: {
        title: '获取分享链接列表',
        uri: '/index/share/linkList',
        type: 'post',
        dataType: 'json',
        params:{
        },
        comment:{
            "message": "成功",
            "status": 1,
            "result": {
                "link_list": {
                    "total": 1,
                    "per_page": 10,
                    "current_page": 1,
                    "data": [
                        {
                            "code": "g2wndicYW//邀请码",
                            "create_time": "2019-11-15 16:54:03",
                            "id": 21,
                            "desc": "微信费率:0.001,支付宝费率:0.001,支付宝转银行卡费率:0.001//邀请描述",
                            "invite_url": "http://www.t.com/Login-register.html?code=g2wndicYW//邀请链接"
                        }
                    ]
                }
            }
        },
    },

    addLink: {
        title: '添加分享链接',
        uri: '/index/share/addLink',
        type: 'post',
        dataType: 'json',
        params:{
            'type_1': 'type_1(支付宝,根据getInitShareInfo接口返回来显示type+类型id)',
            'type_2': 'type_2(根据getInitShareInfo接口返回来显示type+类型id)',
            'type_3': 'type_3(根据getInitShareInfo接口返回来显示type+类型id)',
        },
        comment:{
            "message": "添加成功！",
            "status": 1
        },
    },

    delLink: {
        title: '删除分享链接',
        uri: '/index/share/delLink',
        type: 'post',
        dataType: 'json',
        params:{
            'link_id': 'link_id(分享链接id)',
        },
        comment:{
            "message": "删除成功！",
            "status": 1
        },
    },

};

runGroup("share");

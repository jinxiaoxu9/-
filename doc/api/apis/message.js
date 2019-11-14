apiSelect["message"] = {
    messageList: {
        title: '消息列表',
        uri: '/index/message/messageList',
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
    delMessage: {
        title: '删除消息',
        uri: '/index/message/delMessage',
        type: 'post',
        dataType: 'json',
        params:{
            'message_id': 'message_id(message_id)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result:[
            ]
        },
    },
    readMessage: {
        title: '删阅读消息',
        uri: '/index/message/readMessage',
        type: 'post',
        dataType: 'json',
        params:{
            'message_id': 'message_id(message_id)',
        },
        comment:{
            status : 1,
            msg : "消息",
            url: "",
            result: {
                "info": {
                    "id": 2,
                    "user_id": 5,
                    "title": "title",
                    "content": "content",
                    "add_time": 555533434,
                    "read_time": 1,
                    "is_read": 1,
                    "is_system": 0
                }
            }
        },
    },
    readAllMessage: {
        title: '一键阅读',
        uri: '/index/message/readAllMessage',
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

runGroup("message");

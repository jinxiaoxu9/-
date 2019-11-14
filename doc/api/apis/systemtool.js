apiSelect["systemTool"] = {
    login: {
        title: '文件上传',
        uri: '/index/SysTool/upload',
        type: 'post',
        dataType: 'json',
        params:{
            'file': 'file(文件类容)',
            'path':'path(服务端保存文目录)',
        },
        comment:{
            "message": "上传成功",
            "status": 1,
            "result": {
                "file_name": "uploads/qrcodes/ca945a254517d3e3854a62cd44375b4c_qr.png"
            }
        },

    },
};

runGroup("systemTool");

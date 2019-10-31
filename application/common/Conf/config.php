<?php
return array(
    //数据库配置
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'www_pf_com', // 数据库
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'P@ssw0rd', // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ysk_', // 数据库表前缀
	'OSS_ACCESS_ID'=>'LTAIKc1ooY0Qv21D',
	'OSS_ACCESS_KEY'=>'lPkLzrmoqozZ85OU8v0JBl2zO2Ex2b',
	'OSS_ENDPOINT'=>'oss-cn-beijing.aliyuncs.com',
	'OSS_TEST_BUCKET'=>'tslyxcx',
	'OSS_WEB_SITE'=>'https://img.bukanba.com',
	'OSS_PICEND'=>'',
	'oss_maxSize'=>'1048576',
	'ttk_open'=>'0',
	'tietuku_accesskey'=>'55db290787786fca3916701082583d13f8e6f4b4',
	'tietuku_secretkey'=>'bf7262d9c61f22655fb0152e7bff67c400f46683',
	'tietuku_aid'=>'1274829',
	'tietuku_return_type'=>'s_url',
	 'oss_maxSize'=>1048576,    //1M 
    'oss_exts'   =>array(// 设置附件上传类型    
		'image/jpg',  
		'image/gif',  
		'image/png',  
		'image/jpeg', 
		'image/x-icon', 
		'application/octet-stream',
	),


    'MODULE_ALLOW_LIST'    =>    array('Home','Admin','Gemapay'),
    'DEFAULT_MODULE'       =>    'Home',
    //'URL_MODULE_MAP'    =>    array('admin'=>'admin'),  //模块映射


    'URL_MODEL'=>2, //去掉index.php
    //全局配置
    'SHOW_PAGE_TRACE'=>false,//页面追踪信息
    'AUTH_KEY'             => 'kkVg{EyPWCy:iJ*A-ZW&B+N%WlM^xHEqZGrVG<{,}J)gk``.;u|qD~d!(?"zj;@C', //系统加密KEY，轻易不要修改此项，否则容易造成用户无法登录，如要修改，务必备份原key

     // 全局命名空间
    'AUTOLOAD_NAMESPACE'   => array(
        'Util' => APP_PATH . 'Common/Util/',
    ),
    //引入tags类
    'LOAD_EXT_CONFIG' => 'tags', // 加载扩展配置文件

    //设置上传文件大小
    'UPLOAD_IMAGE_SIZE' =>2,
    // 文件上传默认驱动
    'UPLOAD_DRIVER'        => 'Local',

    // 文件上传相关配置
    'UPLOAD_CONFIG'        => array(
        'mimes'    => '', // 允许上传的文件MiMe类型
        'maxSize'  => 2 * 1024 * 1024, // 上传的文件大小限制 (0-不做限制，默认为2M，后台配置会覆盖此值)
        'autoSub'  => true, // 自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), // 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', // 保存根路径
        'savePath' => '', // 保存路径
        'saveName' => array('uniqid', ''), // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', // 文件保存后缀，空则使用原后缀
        'replace'  => false, // 存在同名是否覆盖
        'hash'     => true, // 是否生成hash编码
        'callback' => false, // 检测文件是否存在回调函数，如果存在返回文件信息数组
    ),

    'SESSION_OPTIONS'         =>  array(
        'name'                =>  'BJYSESSION',                    //设置session名
        'expire'              =>  24*3600*15,                      //SESSION保存15天
        'use_trans_sid'       =>  1,                               //跨页传递
        'use_only_cookies'    =>  0,                               //是否只开启基于cookies的session的会话方式
    ),
//    'TMPL_EXCEPTION_FILE'=>'./404.html',
//    'ERROR_PAGE'            =>  './404.html', // 错误定向页面
    'paginate'               => [
//        'type'      => 'Page\Pagination',//bootstrap  //Page\Pagination
//        'var_page'  => 'page',
//        'list_rows' => 15,
    ],
    'online_time_toqiangdan'=>610,//20分钟

    //是否不发送佣金
    'not_send_bonus' => 1,
   'notify_url' => "http://www.a.com/",
);

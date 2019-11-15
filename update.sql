ALTER TABLE `ysk_recharge`
CHANGE COLUMN `way` `way` VARCHAR(255) NOT NULL COMMENT '充值方式：' ;

ALTER TABLE `ysk_gemapay_code`
ADD COLUMN `is_delete` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_lock`;

ALTER TABLE `ysk_user_invite_setting`
ADD COLUMN `is_delete` INT(1) NOT NULL DEFAULT 0 AFTER `create_time`;

ALTER TABLE `ysk_recharge`
ADD COLUMN `account_name` VARCHAR(225) NOT NULL AFTER `marker`,
ADD COLUMN `account_num` VARCHAR(225) NOT NULL AFTER `account_name`,
ADD COLUMN `bank_name` VARCHAR(225) NOT NULL AFTER `account_num`;

CREATE TABLE `ysk_user_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `read_time` int(11) unsigned NOT NULL DEFAULT '0',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `is_system` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否是系统公告',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx` (`user_id`,`add_time`,`is_read`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=693 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='站内信';

CREATE TABLE `ysk_shop_order` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL COMMENT '所属用户',
  `shop_order_no` VARCHAR(75) NULL COMMENT '商品订单号',
  `shop_type` INT UNSIGNED NULL COMMENT '订单所属商店类型１表示ｐｄｄ，２表示淘宝，３表示转转４,表示京东５，表示国美',
  `create_time` INT UNSIGNED NULL COMMENT '上传时间',
  `price` DECIMAL(10,2) NULL COMMENT '商品金额',
  `vx_pay_h5_link` VARCHAR(445) NULL COMMENT '微信h5链接',
  `zfb_pay_h5_link` VARCHAR(445) NULL COMMENT '支付宝ｈ５链接',
  `vx_pay_pc_link` VARCHAR(445) NULL COMMENT '微信ｐｃ支付链接',
  `zfb_pay_pc_link` VARCHAR(445) NULL COMMENT '支付宝ｐｃ支付链接',
  `status` TINYINT(1) UNSIGNED NULL DEFAULT 0 COMMENT '状态0表示初始化状态１表示正在支付中，２表示支付完成',
  `upload_file` VARCHAR(245) NULL COMMENT '上传原始文件',
  `pay_times` INT UNSIGNED NULL COMMENT '被支付了多少次',
  `finish_time` INT NULL COMMENT '支付完成时间',
  `exprie_time` INT NULL COMMENT '过期时间（提前２个小时过期）',
  `is_delete` TINYINT(1) NULL COMMENT '是否被删除',
  PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 0 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '订单列表';

CREATE TABLE `ysk_user_message` (
`id` INT ( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT ( 10 ) UNSIGNED NOT NULL DEFAULT '0',
`title` VARCHAR ( 32 ) NOT NULL DEFAULT '' COMMENT '标题',
`content` text NOT NULL COMMENT '内容',
`add_time` INT ( 10 ) UNSIGNED NOT NULL DEFAULT '0',
`read_time` INT ( 11 ) UNSIGNED NOT NULL DEFAULT '0',
`is_read` TINYINT ( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否已读',
`is_system` TINYINT ( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否是系统公告',
PRIMARY KEY ( `id` ) USING BTREE,
KEY `idx` ( `user_id`, `add_time`, `is_read` ) USING BTREE
) ENGINE = INNODB AUTO_INCREMENT = 0 DEFAULT CHARSET = utf8 ROW_FORMAT = COMPACT COMMENT = '站内信';

ALTER TABLE `ysk_user`
ADD COLUMN `token` VARCHAR(45) NOT NULL AFTER `security_pwd`;

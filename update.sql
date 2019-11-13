CREATE TABLE `ysk_shop_order` (
  `id` INT UNSIGNED NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`));

ALTER TABLE `ysk_withdraw`
ADD COLUMN `bankcard_id` int(11) NOT NULL default 0 AFTER `status`;



ALTER TABLE `ysk_user`
ADD COLUMN `token` VARCHAR(45) NOT NULL AFTER `security_pwd`;

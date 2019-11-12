ALTER TABLE `ysk_withdraw`
ADD COLUMN `bankcard_id` int(11) NOT NULL default 0 AFTER `status`;



ALTER TABLE `ysk_user`
ADD COLUMN `token` VARCHAR(45) NOT NULL AFTER `security_pwd`;

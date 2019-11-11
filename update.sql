ALTER TABLE `ysk_user`
ADD COLUMN `token` VARCHAR(45) NOT NULL AFTER `security_pwd`;

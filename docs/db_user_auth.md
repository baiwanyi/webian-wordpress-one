-- 系统用户认证表
DROP TABLE IF EXISTS `t_sys_user_auth`;
CREATE TABLE `t_sys_user_auth` (
	`auth_id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
	`user_id` BIGINT(20) NOT NULL COMMENT 'user_id',
	`identity_type` TINYINT(6) NOT NULL DEFAULT '0' COMMENT '登录类型  1-登录账号 2-手机号 3-邮箱  10-微信  11-QQ 12-支付宝 13-微博',
	`identifier` VARCHAR(128) NOT NULL COMMENT '认证标识 ( 用户名 | open_id )',
	`credential` VARCHAR(128) NOT NULL COMMENT '密码凭证',
	`salt` VARCHAR(128) NOT NULL COMMENT 'salt',
    `sys_type` VARCHAR(8) NOT NULL COMMENT '所属系统： MGR-运营平台, MCH-商户中心',
	PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8mb4 COMMENT='系统用户认证表';

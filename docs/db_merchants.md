-- 商户信息表
DROP TABLE IF EXISTS t_mch_info;
CREATE TABLE `t_mch_info` (
        `mch_no` VARCHAR(64) NOT NULL COMMENT '商户号',
        `mch_name` VARCHAR(64) NOT NULL COMMENT '商户名称',
        `mch_short_name` VARCHAR(32) NOT NULL COMMENT '商户简称',
        `type` TINYINT(6) NOT NULL DEFAULT 1 COMMENT '类型: 1-普通商户, 2-特约商户(服务商模式)',
        `isv_no` VARCHAR(64) COMMENT '服务商号',
        `contact_name` VARCHAR(32) COMMENT '联系人姓名',
        `contact_tel` VARCHAR(32) COMMENT '联系人手机号',
        `contact_email` VARCHAR(32) COMMENT '联系人邮箱',
        `state` TINYINT(6) NOT NULL DEFAULT 1 COMMENT '商户状态: 0-停用, 1-正常',
        `remark` VARCHAR(128) COMMENT '商户备注',
        `init_user_id` BIGINT(20) DEFAULT NULL COMMENT '初始用户ID（创建商户时，允许商户登录的用户）',
        `created_uid` BIGINT(20) COMMENT '创建者用户ID',
        `created_by` VARCHAR(64) COMMENT '创建者姓名',
        `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
        `updated_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3) COMMENT '更新时间',
        PRIMARY KEY (`mch_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户信息表';


-- 商户支付通道表 (允许商户  支付方式 对应多个支付接口的配置)
DROP TABLE IF EXISTS t_mch_pay_passage;
CREATE TABLE `t_mch_pay_passage` (
         `id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
         `mch_no` VARCHAR(64) NOT NULL COMMENT '商户号',
         `app_id` VARCHAR(64) NOT NULL COMMENT '应用ID',
         `if_code` VARCHAR(20) NOT NULL COMMENT '支付接口',
         `way_code` VARCHAR(20) NOT NULL COMMENT '支付方式',
         `rate` DECIMAL(20,6) NOT NULL COMMENT '支付方式费率',
         `risk_config` JSON DEFAULT NULL COMMENT '风控数据',
         `state` TINYINT(6) NOT NULL COMMENT '状态: 0-停用, 1-启用',
         `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
         `updated_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3) COMMENT '更新时间',
         PRIMARY KEY (`id`),
         UNIQUE KEY `Uni_AppId_WayCode` (`app_id`,`if_code`, `way_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户支付通道表';

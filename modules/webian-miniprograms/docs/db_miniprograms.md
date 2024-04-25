-- 商户应用表
DROP TABLE IF EXISTS t_mch_app;
CREATE TABLE `t_mch_app` (
         `app_id` varchar(64) NOT NULL COMMENT '应用ID',
         `app_name` varchar(64) NOT NULL DEFAULT '' COMMENT '应用名称',
         `mch_no` VARCHAR(64) NOT NULL COMMENT '商户号',
         `state` TINYINT(6) NOT NULL DEFAULT 1 COMMENT '应用状态: 0-停用, 1-正常',
         `app_secret` VARCHAR(128) NOT NULL COMMENT '应用私钥',
         `remark` varchar(128) DEFAULT NULL COMMENT '备注',
         `created_uid` BIGINT(20) COMMENT '创建者用户ID',
         `created_by` VARCHAR(64) COMMENT '创建者姓名',
         `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
         `updated_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3) COMMENT '更新时间',
         PRIMARY KEY (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户应用表';

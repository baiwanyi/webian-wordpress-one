# 提现系统


## 功能需求
### v1.0.0（2024-03-01）


---
title: 提现订单表
description: 数据库表名：wwpo_pay_order_cashout
updated: 2024年4月28日
---
## 字段
| 字段             | 类型        | 说明                    |
| ---------------- | ----------- | ----------------------- |
| pay_cashout_id   | int(20)     | 自增ID                  |
| user_id          | int(20)     | 关联用户ID              |
| app_id           | string(50)  | 请求应用ID              |
| mch_id           | string(50)  | 支付平台分配的商户号    |
| pay_cashout_no   | string(50)  | 提现订单号              |
| cashout_method   | string(20)  | 提现方式                |
| currency         | string(20)  | 支付货币代码：默认：cny |
| pay_amount       | int(20)     | 支付金额，单位：分      |
| pay_status       | int(20)     | 支付状态，默认：0       |
| pay_account_no   | string(64)  | 收款账号                |
| pay_account_name | string(64)  | 收款人姓名              |
| pay_account_bank | string(32)  | 收款人开户行名称        |
| transfer_desc    | string(100) | 转账备注信息            |
| created_at       | timestamp   | 创建时间                |
| updated_at       | timestamp   | 更新时间                |
| success_time     | timestamp   | 转账成功时间            |


-- 转账订单表d
DROP TABLE IF EXISTS t_transfer_order;
CREATE TABLE `t_transfer_order` (
           `transfer_id` VARCHAR(32) NOT NULL COMMENT '转账订单号',
           `mch_no` VARCHAR(64) NOT NULL COMMENT '商户号',
           `isv_no` VARCHAR(64) COMMENT '服务商号',
           `app_id` VARCHAR(64) NOT NULL COMMENT '应用ID',
           `mch_name` VARCHAR(30) NOT NULL COMMENT '商户名称',
           `mch_type` TINYINT(6) NOT NULL COMMENT '类型: 1-普通商户, 2-特约商户(服务商模式)',
           `mch_order_no` VARCHAR(64) NOT NULL COMMENT '商户订单号',
           `if_code` VARCHAR(20)  NOT NULL COMMENT '支付接口代码',
           `entry_type` VARCHAR(20) NOT NULL COMMENT '入账方式： WX_CASH-微信零钱; ALIPAY_CASH-支付宝转账; BANK_CARD-银行卡',
           `amount` BIGINT(20) NOT NULL COMMENT '转账金额,单位分',
           `currency` VARCHAR(3) NOT NULL DEFAULT 'cny' COMMENT '三位货币代码,人民币:cny',
           `account_no` VARCHAR(64) NOT NULL COMMENT '收款账号',
           `account_name` VARCHAR(64) COMMENT '收款人姓名',
           `bank_name` VARCHAR(32) COMMENT '收款人开户行名称',
           `transfer_desc` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '转账备注信息',
           `client_ip` VARCHAR(32) DEFAULT NULL COMMENT '客户端IP',
           `state` TINYINT(6) NOT NULL DEFAULT '0' COMMENT '支付状态: 0-订单生成, 1-转账中, 2-转账成功, 3-转账失败, 4-订单关闭',
           `channel_extra` VARCHAR(512) DEFAULT NULL COMMENT '特定渠道发起额外参数',
           `channel_order_no` VARCHAR(64) DEFAULT NULL COMMENT '渠道订单号',
           `err_code` VARCHAR(128) DEFAULT NULL COMMENT '渠道支付错误码',
           `err_msg` VARCHAR(256) DEFAULT NULL COMMENT '渠道支付错误描述',
           `ext_param` VARCHAR(128) DEFAULT NULL COMMENT '商户扩展参数',
           `notify_url` VARCHAR(128) NOT NULL default '' COMMENT '异步通知地址',
           `success_time` DATETIME DEFAULT NULL COMMENT '转账成功时间',
           `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
           `updated_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3) COMMENT '更新时间',
           PRIMARY KEY (`transfer_id`),
           UNIQUE KEY `Uni_MchNo_MchOrderNo` (`mch_no`, `mch_order_no`),
           INDEX(`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='转账订单表';

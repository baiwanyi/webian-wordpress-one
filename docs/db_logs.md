---
title: 操作日志表
description: 数据库表名：wwpo_logs
---
## 字段
| 字段         | 类型      | 长度 | 说明                   |
| ------------ | --------- | ---- | ---------------------- |
| log_id       | int       | 20   | 自增ID                 |
| user_id      | int       | 20   | 关联用户ID             |
| user_login   | string    | 100  | 关联用户帐号           |
| app_id       | string    | 50  | 操作请求应用ID         |
| event_code   | int       | 100  | 事件响应代码           |
| event_page   | string    | 255  | 事件请求页面           |
| event_url    | string    | 255  | 事件请求地址           |
| event_method | string    | 100  | 事件请求方式           |
| event_param  | text      | —    | 操作请求参数           |
| event_result | text      | —    | 操作请求结果           |
| user_ip      | string    | 100  | 用户操作IP             |
| user_source  | string    | 100  | 用户操作来源：wx/dy/tt |
| created_at   | timestamp | —    | 创建时间               |


-- 系统操作日志表
DROP TABLE IF EXISTS `t_sys_log`;
CREATE TABLE `t_sys_log` (
  `sys_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` bigint(20) DEFAULT NULL COMMENT '系统用户ID',
  `user_name` varchar(32) DEFAULT NULL COMMENT '用户姓名',
  `user_ip` varchar(128) NOT NULL DEFAULT '' COMMENT '用户IP',
  `sys_type` varchar(8) NOT NULL COMMENT '所属系统： MGR-运营平台, MCH-商户中心',
  `method_name` varchar(128) NOT NULL DEFAULT '' COMMENT '方法名',
  `method_remark` varchar(128) NOT NULL DEFAULT '' COMMENT '方法描述',
  `req_url` varchar(256) NOT NULL DEFAULT '' COMMENT '请求地址',
  `opt_req_param` TEXT DEFAULT NULL COMMENT '操作请求参数',
  `opt_res_info` TEXT DEFAULT NULL COMMENT '操作响应结果',
  `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
  PRIMARY KEY (`sys_log_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COMMENT = '系统操作日志表';

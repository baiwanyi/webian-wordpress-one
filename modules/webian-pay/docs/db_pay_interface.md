---
title: 支付接口表
description: 数据库表名：wwpo_pay_interface
---
## 字段
| 字段             | 类型        | 说明           |
| ---------------- | ----------- | -------------- |
| pay_interface_id | int(20)     | 自增ID         |
| created_uid      | int(20)     | 创建用户ID     |
| app_id           | string(50)  | 请求应用ID     |
| mch_id           | string(50)  | 直连商户号     |
| api_keyid        | string(50)  | API 证书序列号 |
| api_keysecret    | string(50)  | API 证书序内容 |
| api_v2secret     | string(50)  | APIv2密钥      |
| api_v3secret     | string(50)  | APIv3密钥      |
| if_code          | string(20)  | 接口类型代码   |
| if_name          | string(20)  | 接口名称       |
| if_rate          | dec(20,6)   | 支付接口费率   |
| if_status        | string(20)  | 支付接口状态   |
| remark           | string(200) | 备注           |
| created_at       | timestamp   | 创建时间       |
| updated_at       | timestamp   | 更新时间       |

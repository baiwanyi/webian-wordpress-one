---
title: 支付请求结果表
description: 数据库表名：wwpo_pay_order_result
updated: 2024年4月28日
---
## 字段
| 字段          | 类型        | 说明                  |
| ------------- | ----------- | --------------------- |
| pay_result_id | int(20)     | 自增ID                |
| pay_order_no  | string(50)  | 支付订单号            |
| result_status | int(20)     | 状态码                |
| result_code   | string(200) | 错误码                |
| result_text   | string(200) | 错误描述              |
| result_param  | string(255) | 应答参数，prepay_id等 |
| notify_url    | string(200) | 异步通知地址          |
| return_url    | string(200) | 页面跳转地址          |
| client_ip     | string(100) | 客户端IP              |
| created_at    | timestamp   | 发生时间              |

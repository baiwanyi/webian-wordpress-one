---
title: 支付请求结果表
description: 数据库表名：wwpo_pay_order_result
---
## 字段
| 字段           | 类型      | 长度 | 说明                     |
| -------------- | --------- | ---- | ------------------------ |
| pay_result_id   | int       | 20   | 自增ID                   |
| pay_code        | int       | 20   | 关联用户ID               |
| app_id         | string    | 50  | 请求应用ID               |
| mch_id         | string    | 50  | 支付平台分配的商户号               |

| pay_order_no   | string    | 50   | 支付订单号               |
| pay_way_code   | string    | 20   | 支付方式代码             |
| pay_jifen      | int       | 20   | 支付使用积分             |
| pay_amount     | int       | 20   | 支付金额，单位：分       |
| pay_fee_rate   | decimal   | 20,6 | 支付手续费费率           |
| pay_fee_amount | int       | 20   | 支付手续费金额，单位：分 |
| pay_status     | int       | 20   | 支付状态，默认：0        |
| currency       | string    | 20   | 支付货币代码：默认：cny  |
| refund_status  | int       | 20   | 退款状态: 默认：0        |
| refund_times   | int       | 20   | 退款次数: 默认：0        |
| refund_amount  | int       | 20   | 退款金额，单位：分       |
| client_ip      | string    | 100  | 客户端IP                 |
| created_at     | timestamp | —    | 创建时间                 |
| expired_time   | timestamp | —    | 订单失效时间             |
| success_time   | timestamp | —    | 订单支付成功时间         |

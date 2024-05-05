---
title: 退款管理
description: JSAPI支付是指商户通过调用微信支付提供的JSAPI接口，在支付场景中调起微信支付模块完成收款。
updated: 2024-05-05 15:46:33
---
## 业务流程

## 后台界面

## 数据库表
### 退款订单表
`数据库表名` wwpo_pay_order_refund
| 字段                  | 类型        | 说明                           |
| --------------------- | ----------- | ------------------------------ |
| pay_refund_id         | int(20)     | 自增ID                         |
| pay_order_id          | int(20)     | 支付订单号（与pay_orders对应） |
| app_id                | string(50)  | 请求应用ID                     |
| mch_id                | string(50)  | 支付平台分配的商户号           |
| pay_refund_no         | string(50)  | 退款订单号                     |
| user_received_account | string(100) | 退款入账账户                   |
| pay_amount            | int(20)     | 支付金额，单位：分             |
| currency              | string(20)  | 支付货币代码：默认：cny        |
| refund_amount         | int(20)     | 退款金额，单位：分             |
| refund_reason         | string(255) | 退款原因                       |
| refund_status         | string(20)  | 退款状态                       |
| notify_code           | int(3)      | 异步通知状态码                 |
| notify_data           | text        | 异步通知结果                   |
| notify_url            | string(20)  | 异步通知地址                   |
| created_at            | timestamp   | 退款创建时间                   |
| updated_at            | timestamp   | 退款更新时间                   |
| expired_time          | timestamp   | 订单失效时间                   |
| success_time          | timestamp   | 订单退款成功时间               |

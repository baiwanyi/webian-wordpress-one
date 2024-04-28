---
title: 通知记录表
description: 数据库表名：wwpo_pay_order_notify
---
## 字段
| 字段             | 类型        | 说明             |
| ---------------- | ----------- | ---------------- |
| pay_notify_id    | int(20)     | 自增ID           |
| pay_order_no     | string(50)  | 支付订单号       |
| pay_order_type   | string(20)  | 支付订单类型     |
| notify_result_id | string(50)  | 通知的唯一ID     |
| notify_content   | text        | 通知响应结果     |
| notify_url       | string(200) | 通知地址         |
| notify_count     | int(20)     | 通知次数         |
| notify_status    | string(20)  | 通知状态         |
| last_notify_time | timestamp   | 最后一次通知时间 |
| created_at       | timestamp   | 创建时间         |
| updated_at       | timestamp   | 更新时间         |

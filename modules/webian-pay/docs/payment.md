---
title: 支付网关
description: JSAPI支付是指商户通过调用微信支付提供的JSAPI接口，在支付场景中调起微信支付模块完成收款。
updated: 2024-05-05 15:46:33
---
## 接入准备
## 业务流程

## API接口
| 功能列表                    | 描述                                  |
| --------------------------- | ------------------------------------- |
| [支付下单](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [调用支付](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [关闭订单](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [查询订单](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [支付通知](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [退款申请](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |
| [退款通知](./api/prepay.md) | 通过本接口提交微信支付JSAPI支付订单。 |

## 数据库表
### 支付接口配置表
数据库表名：wwpo_pay_interface
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

### 支付订单表
`数据库表名` wwpo_pay_orders
| 字段            | 类型       | 说明                     |
| --------------- | ---------- | ------------------------ |
| pay_order_id    | int(20)    | 自增ID                   |
| pay_app_id      | string(50) | 支付请求的应用ID         |
| pay_mch_id      | string(50) | 支付平台分配的商户号     |
| pay_user_openid | int(20)    | 下单用户ID               |
| pay_order_no    | string(50) | 支付订单号               |
| pay_method      | string(20) | 支付方式                 |
| pay_jifen       | int(20)    | 支付使用积分             |
| pay_amount      | int(20)    | 支付金额，单位：分       |
| pay_fee_rate    | dec(20,6)  | 支付手续费费率           |
| pay_fee_amount  | int(20)    | 支付手续费金额，单位：分 |
| pay_status      | int(20)    | 支付状态，默认：0        |
| currency        | string(20) | 支付货币代码：默认：cny  |
| refund_status   | int(20)    | 退款状态: 默认：0        |
| refund_times    | int(20)    | 退款次数: 默认：0        |
| refund_amount   | int(20)    | 退款金额，单位：分       |
| notify_code     | int(3)     | 异步通知状态码           |
| notify_data     | text       | 异步通知结果             |
| notify_url      | string(20) | 异步通知地址             |
| created_at      | timestamp  | 创建时间                 |
| expired_time    | timestamp  | 订单失效时间             |
| success_time    | timestamp  | 订单支付成功时间         |

### 支付请求结果表
`数据库表名` wwpo_pay_order_result
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

### 订单数据快照表
数据库表名：wwpo_pay_order_snapshot
| 字段                 | 类型      | 说明         |
| -------------------- | --------- | ------------ |
| pay_snapshot_id      | int(20)   | 自增ID       |
| pay_order_id         | int(20)   | 关联用户ID   |
| transaction_snapshot | text      | 交易内容快照 |
| resource_snapshot    | text      | 通知内容快照 |
| created_at           | timestamp | 创建时间     |
| updated_at           | timestamp | 更新时间     |

### 订单通知记录表
数据库表名：wwpo_pay_order_notify
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

## 功能需求
### v1.0.0（2024-03-01）

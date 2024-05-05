---
title: 支付下单下单接口
description: 商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再按Native、JSAPI、APP等不同场景生成交易串调起支付。
updated: 2024.05.05
---
## 接口说明
### 请求方式
`POST` /v3/pay/transactions/jsapi
### 来源
[components/class-wxpay.php#176](../../components/class-wxpay.php)
### 请求参数
| 参数          | 类型    | 说明   |
| ------------- | ------- | ------ |
| pay_result_id | int(20) | 自增ID |

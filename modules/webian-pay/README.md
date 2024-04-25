---
title: 微边支付模块
description: 用于构造小程序端的支付管理。
updated: 2024-04-24 17:22:41
---

## 目录结构
```
模块目录
├─wxpay           微信支付
│   ├─controller      控制器目录
│   └─event.php          事件定义文件
│
├─douyin                抖音支付
│   ├─app.php            应用配置
│   └─view.php           视图配置
│
├─alipay            支付宝
├─route                 路由定义目录
│   ├─route.php          路由定义文件
│   └─ ...
│
├─public                WEB目录（对外访问目录）
│   ├─index.php          入口文件
│   └─.htaccess          用于apache的重写
│
├─autoload.php                 模块加载
├─setting.php                 设置页面
```

## 模块
| 名称                           | 功能                                                  |
| ------------------------------ | ----------------------------------------------------- |
| [仪表盘](./docs/dashboard.md)  | 判断小程序的API，回调，参数，组件等是否在当前版本可用 |
| [支付网关](./docs/payment.md)  | -                                                     |
| [清算系统](./docs/clearing.md) | -                                                     |
| [提现系统](./docs/cashout.md)  | -                                                     |
| [商户系统](./docs/merchant.md) | -                                                     |

## API

## 数据库
| 名称                                                  | 功能           |
| ----------------------------------------------------- | -------------- |
| [pay_orders](./docs/db_pay_orders.md)                 | 支付订单表     |
| [pay_order_result](./docs/db_pay_order_result.md)     | 支付请求结果表 |
| [pay_order_notify](./docs/db_pay_order_notify.md)     | 支付通知记录表 |
| [pay_order_snapshot](./docs/db_pay_order_snapshot.md) | 订单数据快照表 |
| [pay_order_refund](./docs/db_pay_order_refund.md)     | 退款订单表     |
| [pay_order_transfer](./docs/db_pay_order_transfer.md) | 转账订单表     |
| [pay_order_transfer](./docs/db_pay_api.md) | 支付接口表     |

## 技术帮助
微边支付模块是基于官方接口封装，使用前必需先阅官方文档。
 - 微信官方文档：https://mp.weixin.qq.com/wiki
 - 微信支付文档：https://pay.weixin.qq.com/wiki/doc/apiv3/wxpay/pages/index.shtml

## 更新日志
### v1.0.0（2024-03-01）

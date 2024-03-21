# 无边映社模块
更新日期：20240301
模块别名：webian-tv-mall

## 目录结构
```
模块目录
├─wxpay           微信支付
│  ├─controller      控制器目录
│  └─event.php          事件定义文件
│
├─douyin                抖音支付
│  ├─app.php            应用配置
│  └─view.php           视图配置
│
├─alipay            支付宝
├─route                 路由定义目录
│  ├─route.php          路由定义文件
│  └─ ...
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  └─.htaccess          用于apache的重写
│
├─autoload.php                 模块加载
├─setting.php                 设置页面
```

## 模块描述
 - 微信小程序，服务端接口支持
 - 微信认证服务号，服务端接口支持
 - 微信支付（账单、卡券、红包、退款、转账、App支付、JSAPI支付、Web支付、扫码支付等）
 - 支付宝支付（账单、转账、App支付、刷卡支付、扫码支付、Web支付、Wap支付等）
 - 头条支付

## 模块功能
 - [仪表盘](./docs/dashboard.md)
 - [订单系统](./docs/order.md)
 - [支付系统](./docs/payment.md)
 - [清算系统](./docs/clearing.md)
 - [提现系统](./docs/cashout.md)

## 更新日志
### v1.0.0（2024-03-01）
1. `C`开发初始化

# Webian WordPress One 插件
更新日期：20240314

模块别名：webian-wordpress-one

## 目录结构
```
模块目录
├─assets        静态文件目录
│   ├─css       CSS目录
│   ├─images    图片目录
│   └─js        JS目录
│
├─docs          文档目录
├─includes      插件文件目录
│   │
│   ├─class-db-sqlite.php   SQLite数据库类
│   ├─class-wwpo-admin.php  后台管理页面方法类
│
├─languages     语言文件目录
├─modules       模块文件目录
│   ├─index.php          入口文件
│   └─.htaccess          用于apache的重写
│
├─wwpo.php      插件加载文件
```

## 模块描述
 - 微信小程序，服务端接口支持
 - 微信认证服务号，服务端接口支持
 - 微信支付（账单、卡券、红包、退款、转账、App支付、JSAPI支付、Web支付、扫码支付等）
 - 支付宝支付（账单、转账、App支付、刷卡支付、扫码支付、Web支付、Wap支付等）
 - 头条支付
 - 云闪付支付

## 插件功能
- [仪表盘](./docs/dashboard.md)
- [模块管理](./docs/post.md)
- [表单系统](./docs/clearing.md)
- [后台界面](./docs/post.md)
- [插件设置](./docs/post.md)
- [分类增强](./docs/post.md)
- [常用函数](./docs/merchant.md)
- [数据库扩展](./docs/layout.md)

## 模块功能
- [短代码](./docs/merchant.md)
- [日志功能](./docs/log.md)
- [用户管理](./docs/user.md)
- [文章功能](./docs/post.md)
- [优化设置](./docs/post.md)
- [开发工具](./docs/payment.md)
- [登录优化](./docs/post.md)
- [界面优化](./docs/post.md)
- [发布功能](./docs/post.md)

## 插件常量
| 名称      | 简介           | 示例                   |
| --------- | -------------- | ---------------------- |
| NOW       | 定义当前时间戳 | 1710345600             |
| NOW_TIME  | 格式化当前时间 | 2024年3月14日 00:00:00 |
| WWPO_ROLE | 默认权限       | activate_plugins       |

## 数据库
| 名称                           | 功能 |
| ------------------------------ | ---- |
| [wwpo_logs](./docs/db_logs.md)  | 操作日志表   |
| [支付网关](./docs/payment.md)  | -    |
| [清算系统](./docs/clearing.md) | -    |
| [提现系统](./docs/cashout.md)  | -    |
| [商户系统](./docs/merchant.md) | -    |

## 技术帮助
微边支付模块是基于官方接口封装，使用前必需先阅官方文档。
 - [WordPress 官方文档](https://developer.wordpress.org)
 - [REST API 参考手册](https://developer.wordpress.org/rest-api/reference/)

## 更新日志
### v1.0.0（2023-09-01）
### v1.1.0（2024-03-14）
1. `A`
2. `D`

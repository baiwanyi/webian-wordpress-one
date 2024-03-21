# 清算系统
用于商户支付的对账和计算，协助了利益分配的完成。

## 后台菜单

## 结算流程
1. 支付网关费率
2. 业务员佣金比例
3. 平台手续费

## 数据库表
### 分账记录表
pay_order_division_record
| 字段          | 类型       | 说明               |
| ------------- | ---------- | ------------------ |
| record_id     | int(20)    | 自增ID             |
| user_id       | int(20)    | 关联用户ID         |
| source        | string(20) | 所属平台：wx/dy/tt |
| appid         | string(20) | 小程序AppId        |
| jifen         | int(20)    | 积分               |
| player_time   | int(20)    | 观看时长           |
| player_number | int(20)    | 观看短剧数量       |
| created_at    | timestamp  | 创建时间           |
| updated_at    | timestamp  | 更新时间           |

### 商户分账接收者账号组
mch_division_receiver_group

### 商户分账接收者账号绑定关系表
mch_division_receiver

### 转账订单表
transfer_order

## 功能需求
### v1.0.0（2024-03-01）
1. 设定业务员提现起始金额，默认：1000
2. 设定业务员佣金比例，默认：30%
3. 设定业务员提现周期，默认：T+7
4. 设定平台手续费，默认：0%

# 操作日志表
## 字段
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

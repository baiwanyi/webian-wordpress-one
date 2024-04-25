
-- 订单接口数据快照（加密存储）
DROP TABLE IF EXISTS `t_order_snapshot`;
CREATE TABLE `t_order_snapshot` (
        `order_id` VARCHAR(64) NOT NULL COMMENT '订单ID',
        `order_type` TINYINT(6) NOT NULL COMMENT '订单类型: 1-支付, 2-退款',
        `mch_req_data` TEXT DEFAULT NULL COMMENT '下游请求数据',
        `mch_req_time` DATETIME DEFAULT NULL COMMENT '下游请求时间',
        `mch_resp_data` TEXT DEFAULT NULL COMMENT '向下游响应数据',
        `mch_resp_time` DATETIME DEFAULT NULL COMMENT '向下游响应时间',
        `channel_req_data` TEXT DEFAULT NULL COMMENT '向上游请求数据',
        `channel_req_time` DATETIME DEFAULT NULL COMMENT '向上游请求时间',
        `channel_resp_data` TEXT DEFAULT NULL COMMENT '上游响应数据',
        `channel_resp_time` DATETIME DEFAULT NULL COMMENT '上游响应时间',
        `created_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT '创建时间',
        `updated_at` TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3) COMMENT '更新时间',
        PRIMARY KEY (`order_id`, `order_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单接口数据快照';

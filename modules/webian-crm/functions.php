<?php

/**
 * 客户关系管理模块应用函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Webian-CRM
 */

/**
 * 省市区域显示函数
 *
 * @since 1.0.0
 * @param string $province  省份拼音
 * @param string $city      城市编号
 */
function wwpo_crm_get_region($province, $city = null)
{
    $region['guangxi'] = [
        450100 => '南宁市',
        450200 => '柳州市',
        450300 => '桂林市',
        450400 => '梧州市',
        450500 => '北海市',
        450600 => '防城港市',
        450700 => '钦州市',
        450800 => '贵港市',
        450900 => '玉林市',
        451000 => '百色市',
        451100 => '贺州市',
        451200 => '河池市',
        451300 => '来宾市',
        451400 => '崇左市'
    ];

    if (isset($city)) {
        return $region[$province][$city] ?? '';
    }

    return $region[$province] ?? [];
}

/**
 * 客户等级显示函数
 *
 * @since 1.0.0
 * @param string $rank
 */
function wwpo_crm_customer_rank($rank = null)
{
    $array_rank = ['普通', '一星', '二星', '三星', '四星', '五星', '六星', '七星', '八星', '九星'];

    if (isset($rank)) {
        return $array_rank[$rank] ?? '';
    }

    return $array_rank;
}

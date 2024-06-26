<?php

/**
 * 微信支付名词表
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxpay
 */
class WWPO_Pay_Names
{
    /**
     * 开户银行对照表
     *
     * @since 1.0.0
     * @param string $bank_type 银行别名
     */
    static function bank($bank_type)
    {
        $bank_type = strtoupper($bank_type);

        switch ($bank_type) {
            case 'ICBC_DEBIT':
                return '工商银行（借记卡）';
            case 'ICBC_CREDIT':
                return '工商银行（信用卡）';
            case 'ABC_DEBIT':
                return '农业银行（借记卡）';
            case 'ABC_CREDIT':
                return '农业银行（信用卡）';
            case 'PSBC_CREDIT':
                return '邮储银行（信用卡）';
            case 'PSBC_DEBIT':
                return '邮储银行（借记卡）';
            case 'CCB_DEBIT':
                return '建设银行（借记卡）';
            case 'CCB_CREDIT':
                return '建设银行（信用卡）';
            case 'CMB_DEBIT':
                return '招商银行（借记卡）';
            case 'CMB_CREDIT':
                return '招商银行（信用卡）';
            case 'BOC_DEBIT':
                return '中国银行（借记卡）';
            case 'BOC_CREDIT':
                return '中国银行（信用卡）';
            case 'COMM_DEBIT':
                return '交通银行（借记卡）';
            case 'COMM_CREDIT':
                return '交通银行（信用卡）';
            case 'SPDB_DEBIT':
                return '浦发银行（借记卡）';
            case 'SPDB_CREDIT':
                return '浦发银行（信用卡）';
            case 'GDB_DEBIT':
                return '广发银行（借记卡）';
            case 'GDB_CREDIT':
                return '广发银行（信用卡）';
            case 'CMBC_DEBIT':
                return '民生银行（借记卡）';
            case 'CMBC_CREDIT':
                return '民生银行（信用卡）';
            case 'PAB_DEBIT':
                return '平安银行（借记卡）';
            case 'PAB_CREDIT':
                return '平安银行（信用卡）';
            case 'CEB_DEBIT':
                return '光大银行（借记卡）';
            case 'CEB_CREDIT':
                return '光大银行（信用卡）';
            case 'CIB_DEBIT':
                return '兴业银行（借记卡）';
            case 'CIB_CREDIT':
                return '兴业银行（信用卡）';
            case 'CITIC_DEBIT':
                return '中信银行（借记卡）';
            case 'CITIC_CREDIT':
                return '中信银行（信用卡）';
            case 'BOSH_DEBIT':
                return '上海银行（借记卡）';
            case 'BOSH_CREDIT':
                return '上海银行（信用卡）';
            case 'AHRCUB_CREDIT':
                return '安徽省农村信用社联合社（信用卡）';
            case 'AHRCUB_DEBIT':
                return '安徽省农村信用社联合社（借记卡）';
            case 'AIB_DEBIT':
                return '百信银行（借记卡）';
            case 'ASCB_DEBIT':
                return '鞍山银行（借记卡）';
            case 'ATRB_DEBIT':
                return '盘山安泰村镇银行（借记卡）';
            case 'BCZ_CREDIT':
                return '沧州银行（信用卡）';
            case 'BCZ_DEBIT':
                return '沧州银行（借记卡）';
            case 'BDB_DEBIT':
                return '保定银行（借记卡）';
            case 'BEEB_CREDIT':
                return '鄞州银行（信用卡）';
            case 'BEEB_DEBIT':
                return '鄞州银行（借记卡）';
            case 'BGZB_DEBIT':
                return '贵州银行（借记卡）';
            case 'BHB_CREDIT':
                return '河北银行（信用卡）';
            case 'BHB_DEBIT':
                return '河北银行（借记卡）';
            case 'BJRCB_CREDIT':
                return '北京农商行（信用卡）';
            case 'BJRCB_DEBIT':
                return '北京农商行（借记卡）';
            case 'BNC_CREDIT':
                return '江西银行（信用卡）';
            case 'BNC_DEBIT':
                return '江西银行（借记卡）';
            case 'BOB_CREDIT':
                return '北京银行（信用卡）';
            case 'BOB_DEBIT':
                return '北京银行（借记卡）';
            case 'BOBBG_CREDIT':
                return '北部湾银行（信用卡）';
            case 'BOBBG_DEBIT':
                return '北部湾银行（借记卡）';
            case 'BOCD_DEBIT':
                return '成都银行（借记卡）';
            case 'BOCDB_DEBIT':
                return '承德银行（借记卡）';
            case 'BOCFB_DEBIT':
                return '中银富登村镇银行（借记卡）';
            case 'BOCTS_DEBIT':
                return '焦作中旅银行（借记卡）';
            case 'BOD_CREDIT':
                return '东莞银行（信用卡）';
            case 'BOD_DEBIT':
                return '东莞银行（借记卡）';
            case 'BOFS_DEBIT':
                return '抚顺银行（借记卡）';
            case 'BOHN_DEBIT':
                return '海南银行（借记卡）';
            case 'BOIMCB_CREDIT':
                return '内蒙古银行（信用卡）';
            case 'BOIMCB_DEBIT':
                return '内蒙古银行（借记卡）';
            case 'BOJN_DEBIT':
                return '济宁银行（借记卡）';
            case 'BOJX_DEBIT':
                return '嘉兴银行（借记卡）';
            case 'BOLB_DEBIT':
                return '洛阳银行（借记卡）';
            case 'BOLFB_DEBIT':
                return '廊坊银行（借记卡）';
            case 'BONX_CREDIT':
                return '宁夏银行（信用卡）';
            case 'BONX_DEBIT':
                return '宁夏银行（借记卡）';
            case 'BOPDS_DEBIT':
                return '平顶山银行（借记卡）';
            case 'BOPJ_DEBIT':
                return '盘锦银行（借记卡）';
            case 'BOQHB_CREDIT':
                return '青海银行（信用卡）';
            case 'BOQHB_DEBIT':
                return '青海银行（借记卡）';
            case 'BOSXB_DEBIT':
                return '绍兴银行（借记卡）';
            case 'BOSZS_DEBIT':
                return '石嘴山银行（借记卡）';
            case 'BOTSB_DEBIT':
                return '唐山银行（借记卡）';
            case 'BOZ_CREDIT':
                return '张家口银行（信用卡）';
            case 'BOZ_DEBIT':
                return '张家口银行（借记卡）';
            case 'BSB_CREDIT':
                return '包商银行（信用卡）';
            case 'BSB_DEBIT':
                return '包商银行（借记卡）';
            case 'BYK_DEBIT':
                return '营口银行（借记卡）';
            case 'CBHB_DEBIT':
                return '渤海银行（借记卡）';
            case 'CCAB_CREDIT':
                return '长安银行（信用卡）';
            case 'CCAB_DEBIT':
                return '长安银行（借记卡）';
            case 'CDRCB_DEBIT':
                return '成都农商银行（借记卡）';
            case 'CITIB_CREDIT':
                return '花旗银行（信用卡）';
            case 'CITIB_DEBIT':
                return '花旗银行（借记卡）';
            case 'CJCCB_DEBIT':
                return '江苏长江商业银行（借记卡）';
            case 'CQB_CREDIT':
                return '重庆银行（信用卡）';
            case 'CQB_DEBIT':
                return '重庆银行（借记卡）';
            case 'CQRCB_CREDIT':
                return '重庆农村商业银行（信用卡）';
            case 'CQRCB_DEBIT':
                return '重庆农村商业银行（借记卡）';
            case 'CQTGB_DEBIT':
                return '重庆三峡银行（借记卡）';
            case 'CRB_CREDIT':
                return '珠海华润银行（信用卡）';
            case 'CRB_DEBIT':
                return '珠海华润银行（借记卡）';
            case 'CSCB_CREDIT':
                return '长沙银行（信用卡）';
            case 'CSCB_DEBIT':
                return '长沙银行（借记卡）';
            case 'CSRCB_CREDIT':
                return '常熟农商银行（信用卡）';
            case 'CSRCB_DEBIT':
                return '常熟农商银行（借记卡）';
            case 'CSXB_DEBIT':
                return '三湘银行（借记卡）';
            case 'CYCB_CREDIT':
                return '朝阳银行（信用卡）';
            case 'CYCB_DEBIT':
                return '朝阳银行（借记卡）';
            case 'CZB_CREDIT':
                return '浙商银行（信用卡）';
            case 'CZB_DEBIT':
                return '浙商银行（借记卡）';
            case 'CZCB_CREDIT':
                return '稠州银行（信用卡）';
            case 'CZCB_DEBIT':
                return '稠州银行（借记卡）';
            case 'CZCCB_DEBIT':
                return '长治银行（借记卡）';
            case 'DANDONGB_CREDIT':
                return '丹东银行（信用卡）';
            case 'DANDONGB_DEBIT':
                return '丹东银行（借记卡）';
            case 'DBSB_DEBIT':
                return '星展银行（借记卡）';
            case 'DCSFRB_DEBIT':
                return '大城舜丰村镇银行（借记卡）';
            case 'DHDYB_DEBIT':
                return '德惠敦银村镇银行（借记卡）';
            case 'DHRB_DEBIT':
                return '调兵山惠民村镇银行（借记卡）';
            case 'DLB_CREDIT':
                return '大连银行（信用卡）';
            case 'DLB_DEBIT':
                return '大连银行（借记卡）';
            case 'DLRCB_DEBIT':
                return '大连农商行（借记卡）';
            case 'DRCB_CREDIT':
                return '东莞农商银行（信用卡）';
            case 'DRCB_DEBIT':
                return '东莞农商银行（借记卡）';
            case 'DSB_DEBIT':
                return '大新银行（借记卡）';
            case 'DTCCB_DEBIT':
                return '大同银行（借记卡）';
            case 'DYB_CREDIT':
                return '东营银行（信用卡）';
            case 'DYB_DEBIT':
                return '东营银行（借记卡）';
            case 'DYCCB_DEBIT':
                return '长城华西银行（借记卡）';
            case 'DYLSB_DEBIT':
                return '东营莱商村镇银行（借记卡）';
            case 'DZB_DEBIT':
                return '德州银行（借记卡）';
            case 'DZCCB_DEBIT':
                return '达州银行（借记卡）';
            case 'EDRB_DEBIT':
                return '鼎业村镇银行（借记卡）';
            case 'ESUNB_DEBIT':
                return '玉山银行（借记卡）';
            case 'FBB_DEBIT':
                return '富邦华一银行（借记卡）';
            case 'FDB_CREDIT':
                return '富滇银行（信用卡）';
            case 'FDB_DEBIT':
                return '富滇银行（借记卡）';
            case 'FJHXB_CREDIT':
                return '福建海峡银行（信用卡）';
            case 'FJHXB_DEBIT':
                return '福建海峡银行（借记卡）';
            case 'FJNX_CREDIT':
                return '福建农信银行（信用卡）';
            case 'FJNX_DEBIT':
                return '福建农信银行（借记卡）';
            case 'FUXINB_CREDIT':
                return '阜新银行（信用卡）';
            case 'FUXINB_DEBIT':
                return '阜新银行（借记卡）';
            case 'FXLZB_DEBIT':
                return '费县梁邹村镇银行（借记卡）';
            case 'GADRB_DEBIT':
                return '贵安新区发展村镇银行（借记卡）';
            case 'GDHX_DEBIT':
                return '广东华兴银行（借记卡）';
            case 'GDNYB_CREDIT':
                return '南粤银行（信用卡）';
            case 'GDNYB_DEBIT':
                return '南粤银行（借记卡）';
            case 'GDRCU_DEBIT':
                return '广东农信银行（借记卡）';
            case 'GLB_CREDIT':
                return '桂林银行（信用卡）';
            case 'GLB_DEBIT':
                return '桂林银行（借记卡）';
            case 'GLGMCB_DEBIT':
                return '桂林国民村镇银行（借记卡）';
            case 'GRCB_CREDIT':
                return '广州农商银行（信用卡）';
            case 'GRCB_DEBIT':
                return '广州农商银行（借记卡）';
            case 'GSB_DEBIT':
                return '甘肃银行（借记卡）';
            case 'GSNX_DEBIT':
                return '甘肃农信（借记卡）';
            case 'GSRB_DEBIT':
                return '广阳舜丰村镇银行（借记卡）';
            case 'GXNX_CREDIT':
                return '广西农信（信用卡）';
            case 'GXNX_DEBIT':
                return '广西农信（借记卡）';
            case 'GYCB_CREDIT':
                return '贵阳银行（信用卡）';
            case 'GYCB_DEBIT':
                return '贵阳银行（借记卡）';
            case 'GZCB_CREDIT':
                return '广州银行（信用卡）';
            case 'GZCB_DEBIT':
                return '广州银行（借记卡）';
            case 'GZCCB_CREDIT':
                return '赣州银行（信用卡）';
            case 'GZCCB_DEBIT':
                return '赣州银行（借记卡）';
            case 'GZNX_DEBIT':
                return '贵州农信（借记卡）';
            case 'HAINNX_CREDIT':
                return '海南农信（信用卡）';
            case 'HAINNX_DEBIT':
                return '海南农信（借记卡）';
            case 'HANAB_DEBIT':
                return '韩亚银行（借记卡）';
            case 'HBCB_CREDIT':
                return '湖北银行（信用卡）';
            case 'HBCB_DEBIT':
                return '湖北银行（借记卡）';
            case 'HBNX_CREDIT':
                return '湖北农信（信用卡）';
            case 'HBNX_DEBIT':
                return '湖北农信（借记卡）';
            case 'HDCB_DEBIT':
                return '邯郸银行（借记卡）';
            case 'HEBNX_DEBIT':
                return '河北农信（借记卡）';
            case 'HFB_CREDIT':
                return '恒丰银行（信用卡）';
            case 'HFB_DEBIT':
                return '恒丰银行（借记卡）';
            case 'HKB_CREDIT':
                return '汉口银行（信用卡）';
            case 'HKB_DEBIT':
                return '汉口银行（借记卡）';
            case 'HKBEA_CREDIT':
                return '东亚银行（信用卡）';
            case 'HKBEA_DEBIT':
                return '东亚银行（借记卡）';
            case 'HKUB_DEBIT':
                return '海口联合农商银行（借记卡）';
            case 'HLDCCB_DEBIT':
                return '葫芦岛银行（借记卡）';
            case 'HLDYB_DEBIT':
                return '和龙敦银村镇银行（借记卡）';
            case 'HLJRCUB_DEBIT':
                return '黑龙江农信社（借记卡）';
            case 'HMCCB_DEBIT':
                return '哈密银行（借记卡）';
            case 'HNNX_DEBIT':
                return '河南农信（借记卡）';
            case 'HRBB_CREDIT':
                return '哈尔滨银行（信用卡）';
            case 'HRBB_DEBIT':
                return '哈尔滨银行（借记卡）';
            case 'HRCB_DEBIT':
                return '保德慧融村镇银行（借记卡）';
            case 'HRXJB_CREDIT':
                return '华融湘江银行（信用卡）';
            case 'HRXJB_DEBIT':
                return '华融湘江银行（借记卡）';
            case 'HSB_CREDIT':
                return '徽商银行（信用卡）';
            case 'HSB_DEBIT':
                return '徽商银行（借记卡）';
            case 'HSBC_DEBIT':
                return '恒生银行（借记卡）';
            case 'HSBCC_CREDIT':
                return '汇丰银行（信用卡）';
            case 'HSBCC_DEBIT':
                return '汇丰银行（借记卡）';
            case 'HSCB_DEBIT':
                return '衡水银行（借记卡）';
            case 'HUIHEB_DEBIT':
                return '新疆汇和银行（借记卡）';
            case 'HUNNX_DEBIT':
                return '湖南农信（借记卡）';
            case 'HUSRB_DEBIT':
                return '湖商村镇银行（借记卡）';
            case 'HXB_CREDIT':
                return '华夏银行（信用卡）';
            case 'HXB_DEBIT':
                return '华夏银行（借记卡）';
            case 'HZB_CREDIT':
                return '杭州银行（信用卡）';
            case 'HZB_DEBIT':
                return '杭州银行（借记卡）';
            case 'HZCCB_DEBIT':
                return '湖州银行（借记卡）';
            case 'IBKB_DEBIT':
                return '企业银行（借记卡）';
            case 'JCB_DEBIT':
                return '晋城银行（借记卡）';
            case 'JCBK_CREDIT':
                return '晋城银行（信用卡）';
            case 'JDHDB_DEBIT':
                return '上海嘉定洪都村镇银行（借记卡）';
            case 'JDZCCB_DEBIT':
                return '景德镇市商业银行（借记卡）';
            case 'JHCCB_CREDIT':
                return '金华银行（信用卡）';
            case 'JHCCB_DEBIT':
                return '金华银行（借记卡）';
            case 'JJCCB_CREDIT':
                return '九江银行（信用卡）';
            case 'JJCCB_DEBIT':
                return '九江银行（借记卡）';
            case 'JLB_CREDIT':
                return '吉林银行（信用卡）';
            case 'JLB_DEBIT':
                return '吉林银行（借记卡）';
            case 'JLNX_DEBIT':
                return '吉林农信（借记卡）';
            case 'JNRCB_CREDIT':
                return '江南农商行（信用卡）';
            case 'JNRCB_DEBIT':
                return '江南农商行（借记卡）';
            case 'JRCB_CREDIT':
                return '江阴农商行（信用卡）';
            case 'JRCB_DEBIT':
                return '江阴农商行（借记卡）';
            case 'JSB_CREDIT':
                return '江苏银行（信用卡）';
            case 'JSB_DEBIT':
                return '江苏银行（借记卡）';
            case 'JSHB_CREDIT':
                return '晋商银行（信用卡）';
            case 'JSHB_DEBIT':
                return '晋商银行（借记卡）';
            case 'JSNX_CREDIT':
                return '江苏农信（信用卡）';
            case 'JSNX_DEBIT':
                return '江苏农信（借记卡）';
            case 'JUFENGB_DEBIT':
                return '临朐聚丰村镇银行（借记卡）';
            case 'JXB_DEBIT':
                return '西昌金信村镇银行（借记卡）';
            case 'JXNXB_DEBIT':
                return '江西农信（借记卡）';
            case 'JZB_CREDIT':
                return '晋中银行（信用卡）';
            case 'JZB_DEBIT':
                return '晋中银行（借记卡）';
            case 'JZCB_CREDIT':
                return '锦州银行（信用卡）';
            case 'JZCB_DEBIT':
                return '锦州银行（借记卡）';
            case 'KCBEB_DEBIT':
                return '天津金城银行（借记卡）';
            case 'KLB_CREDIT':
                return '昆仑银行（信用卡）';
            case 'KLB_DEBIT':
                return '昆仑银行（借记卡）';
            case 'KRCB_DEBIT':
                return '昆山农商（借记卡）';
            case 'KSHB_DEBIT':
                return '梅州客商银行（借记卡）';
            case 'KUERLECB_DEBIT':
                return '库尔勒市商业银行（借记卡）';
            case 'LCYRB_DEBIT':
                return '陵城圆融村镇银行（借记卡）';
            case 'LICYRB_DEBIT':
                return '历城圆融村镇银行（借记卡）';
            case 'LJB_DEBIT':
                return '龙江银行（借记卡）';
            case 'LLB_DEBIT':
                return '山东兰陵村镇银行（借记卡）';
            case 'LLHZCB_DEBIT':
                return '柳林汇泽村镇银行（借记卡）';
            case 'LNNX_DEBIT':
                return '辽宁农信（借记卡）';
            case 'LPCB_DEBIT':
                return '凉山州商业银行（借记卡）';
            case 'LPSBLVB_DEBIT':
                return '钟山凉都村镇银行（借记卡）';
            case 'LSB_CREDIT':
                return '临商银行（信用卡）';
            case 'LSB_DEBIT':
                return '临商银行（借记卡）';
            case 'LSCCB_DEBIT':
                return '乐山市商业银行（借记卡）';
            case 'LUZB_DEBIT':
                return '柳州银行（借记卡）';
            case 'LWB_DEBIT':
                return '莱商银行（借记卡）';
            case 'LYYHB_DEBIT':
                return '辽阳银行（借记卡）';
            case 'LZB_CREDIT':
                return '兰州银行（信用卡）';
            case 'LZB_DEBIT':
                return '兰州银行（借记卡）';
            case 'LZCCB_DEBIT':
                return '泸州市商业银行（借记卡）';
            case 'MHBRB_DEBIT':
                return '闵行上银村镇银行（借记卡）';
            case 'MINTAIB_CREDIT':
                return '民泰银行（信用卡）';
            case 'MINTAIB_DEBIT':
                return '民泰银行（借记卡）';
            case 'MPJDRB_DEBIT':
                return '牟平胶东村镇银行（借记卡）';
            case 'MYCCB_DEBIT':
                return '绵阳市商业银行（借记卡）';
            case 'NBCB_CREDIT':
                return '宁波银行（信用卡）';
            case 'NBCB_DEBIT':
                return '宁波银行（借记卡）';
            case 'NCB_DEBIT':
                return '宁波通商银行（借记卡）';
            case 'NCBCB_DEBIT':
                return '南洋商业银行（借记卡）';
            case 'NCCB_DEBIT':
                return '四川天府银行（借记卡）';
            case 'NJCB_CREDIT':
                return '南京银行（信用卡）';
            case 'NJCB_DEBIT':
                return '南京银行（借记卡）';
            case 'NJJDRB_DEBIT':
                return '宁津胶东村镇银行（借记卡）';
            case 'NJXLRB_DEBIT':
                return '内江兴隆村镇银行（借记卡）';
            case 'NMGNX_DEBIT':
                return '内蒙古农信（借记卡）';
            case 'NNGMB_DEBIT':
                return '南宁江南国民村镇银行（借记卡）';
            case 'NUB_DEBIT':
                return '辽宁振兴银行（借记卡）';
            case 'NYCCB_DEBIT':
                return '南阳村镇银行（借记卡）';
            case 'OCBCWHCB_DEBIT':
                return '华侨永亨银行（借记卡）';
            case 'OHVB_DEBIT':
                return '鄂托克旗汇泽村镇银行（借记卡）';
            case 'ORDOSB_CREDIT':
                return '鄂尔多斯银行（信用卡）';
            case 'ORDOSB_DEBIT':
                return '鄂尔多斯银行（借记卡）';
            case 'PBDLRB_DEBIT':
                return '平坝鼎立村镇银行（借记卡）';
            case 'PJDWHFB_DEBIT':
                return '大洼恒丰村镇银行（借记卡）';
            case 'PJJYRB_DEBIT':
                return '浦江嘉银村镇银行（借记卡）';
            case 'PZHCCB_DEBIT':
                return '攀枝花银行（借记卡）';
            case 'QDCCB_CREDIT':
                return '青岛银行（信用卡）';
            case 'QDCCB_DEBIT':
                return '青岛银行（借记卡）';
            case 'QHDB_DEBIT':
                return '秦皇岛银行（借记卡）';
            case 'QHJDRB_DEBIT':
                return '齐河胶东村镇银行（借记卡）';
            case 'QHNX_DEBIT':
                return '青海农信（借记卡）';
            case 'QJSYB_DEBIT':
                return '衢江上银村镇银行（借记卡）';
            case 'QLB_CREDIT':
                return '齐鲁银行（信用卡）';
            case 'QLB_DEBIT':
                return '齐鲁银行（借记卡）';
            case 'QLVB_DEBIT':
                return '青隆村镇银行（借记卡）';
            case 'QSB_CREDIT':
                return '齐商银行（信用卡）';
            case 'QSB_DEBIT':
                return '齐商银行（借记卡）';
            case 'QZCCB_CREDIT':
                return '泉州银行（信用卡）';
            case 'QZCCB_DEBIT':
                return '泉州银行（借记卡）';
            case 'RHCB_DEBIT':
                return '长子县融汇村镇银行（借记卡）';
            case 'RQCZB_DEBIT':
                return '任丘村镇银行（借记卡）';
            case 'RXYHB_DEBIT':
                return '瑞信村镇银行（借记卡）';
            case 'RZB_DEBIT':
                return '日照银行（借记卡）';
            case 'SCB_CREDIT':
                return '渣打银行（信用卡）';
            case 'SCB_DEBIT':
                return '渣打银行（借记卡）';
            case 'SCNX_DEBIT':
                return '四川农信（借记卡）';
            case 'SDEB_CREDIT':
                return '顺德农商行（信用卡）';
            case 'SDEB_DEBIT':
                return '顺德农商行（借记卡）';
            case 'SDRCU_DEBIT':
                return '山东农信（借记卡）';
            case 'SHHJB_DEBIT':
                return '商河汇金村镇银行（借记卡）';
            case 'SHINHAN_DEBIT':
                return '新韩银行（借记卡）';
            case 'SHRB_DEBIT':
                return '上海华瑞银行（借记卡）';
            case 'SJB_CREDIT':
                return '盛京银行（信用卡）';
            case 'SJB_DEBIT':
                return '盛京银行（借记卡）';
            case 'SNB_DEBIT':
                return '苏宁银行（借记卡）';
            case 'SNCCB_DEBIT':
                return '遂宁银行（借记卡）';
            case 'SPDYB_DEBIT':
                return '四平铁西敦银村镇银行（借记卡）';
            case 'SRB_DEBIT':
                return '上饶银行（借记卡）';
            case 'SRCB_CREDIT':
                return '上海农商银行（信用卡）';
            case 'SRCB_DEBIT':
                return '上海农商银行（借记卡）';
            case 'SUZB_CREDIT':
                return '苏州银行（信用卡）';
            case 'SUZB_DEBIT':
                return '苏州银行（借记卡）';
            case 'SXNX_DEBIT':
                return '山西农信（借记卡）';
            case 'SXXH_DEBIT':
                return '陕西信合（借记卡）';
            case 'SZRCB_CREDIT':
                return '深圳农商银行（信用卡）';
            case 'SZRCB_DEBIT':
                return '深圳农商银行（借记卡）';
            case 'TACCB_CREDIT':
                return '泰安银行（信用卡）';
            case 'TACCB_DEBIT':
                return '泰安银行（借记卡）';
            case 'TCRCB_DEBIT':
                return '太仓农商行（借记卡）';
            case 'TJB_CREDIT':
                return '天津银行（信用卡）';
            case 'TJB_DEBIT':
                return '天津银行（借记卡）';
            case 'TJBHB_CREDIT':
                return '天津滨海农商行（信用卡）';
            case 'TJBHB_DEBIT':
                return '天津滨海农商行（借记卡）';
            case 'TJHMB_DEBIT':
                return '天津华明村镇银行（借记卡）';
            case 'TJNHVB_DEBIT':
                return '天津宁河村镇银行（借记卡）';
            case 'TLB_DEBIT':
                return '铁岭银行（借记卡）';
            case 'TLVB_DEBIT':
                return '铁岭新星村镇银行（借记卡）';
            case 'TMDYB_DEBIT':
                return '图们敦银村镇银行（借记卡）';
            case 'TRCB_CREDIT':
                return '天津农商（信用卡）';
            case 'TRCB_DEBIT':
                return '天津农商（借记卡）';
            case 'TZB_CREDIT':
                return '台州银行（信用卡）';
            case 'TZB_DEBIT':
                return '台州银行（借记卡）';
            case 'UOB_DEBIT':
                return '大华银行（借记卡）';
            case 'URB_DEBIT':
                return '联合村镇银行（借记卡）';
            case 'VBCB_DEBIT':
                return '村镇银行（借记卡）';
            case 'WACZB_DEBIT':
                return '武安村镇银行（借记卡）';
            case 'WB_DEBIT':
                return '友利银行（借记卡）';
            case 'WEB_DEBIT':
                return '微众银行（借记卡）';
            case 'WEGOB_DEBIT':
                return '蓝海银行（借记卡）';
            case 'WFB_CREDIT':
                return '潍坊银行（信用卡）';
            case 'WFB_DEBIT':
                return '潍坊银行（借记卡）';
            case 'WHB_CREDIT':
                return '威海商业银行（信用卡）';
            case 'WHB_DEBIT':
                return '威海商业银行（借记卡）';
            case 'WHRC_CREDIT':
                return '武汉农商行（信用卡）';
            case 'WHRC_DEBIT':
                return '武汉农商行（借记卡）';
            case 'WHRYVB_DEBIT':
                return '芜湖圆融村镇银行（借记卡）';
            case 'WJRCB_CREDIT':
                return '吴江农商行（信用卡）';
            case 'WJRCB_DEBIT':
                return '吴江农商行（借记卡）';
            case 'WLMQB_CREDIT':
                return '乌鲁木齐银行（信用卡）';
            case 'WLMQB_DEBIT':
                return '乌鲁木齐银行（借记卡）';
            case 'WRCB_CREDIT':
                return '无锡农商行（信用卡）';
            case 'WRCB_DEBIT':
                return '无锡农商行（借记卡）';
            case 'WUHAICB_DEBIT':
                return '乌海银行（借记卡）';
            case 'WZB_CREDIT':
                return '温州银行（信用卡）';
            case 'WZB_DEBIT':
                return '温州银行（借记卡）';
            case 'WZMSB_DEBIT':
                return '温州民商（借记卡）';
            case 'XAB_CREDIT':
                return '西安银行（信用卡）';
            case 'XAB_DEBIT':
                return '西安银行（借记卡）';
            case 'XCXPB_DEBIT':
                return '许昌新浦村镇银行（借记卡）';
            case 'XHB_DEBIT':
                return '大连鑫汇村镇银行（借记卡）';
            case 'XHNMB_DEBIT':
                return '安顺西航南马村镇银行（借记卡）';
            case 'XIB_DEBIT':
                return '厦门国际银行（借记卡）';
            case 'XINANB_DEBIT':
                return '安徽新安银行（借记卡）';
            case 'XJB_DEBIT':
                return '新疆银行（借记卡）';
            case 'XJJDRB_DEBIT':
                return '夏津胶东村镇银行（借记卡）';
            case 'XJRCCB_DEBIT':
                return '新疆农信银行（借记卡）';
            case 'XMCCB_CREDIT':
                return '厦门银行（信用卡）';
            case 'XMCCB_DEBIT':
                return '厦门银行（借记卡）';
            case 'XRTB_DEBIT':
                return '元氏信融村镇银行（借记卡）';
            case 'XTB_CREDIT':
                return '邢台银行（信用卡）';
            case 'XTB_DEBIT':
                return '邢台银行（借记卡）';
            case 'XWB_DEBIT':
                return '新网银行（借记卡）';
            case 'XXCB_DEBIT':
                return '新乡银行（借记卡）';
            case 'XXHZCB_DEBIT':
                return '兴县汇泽村镇银行（借记卡）';
            case 'XXRB_DEBIT':
                return '新乡新兴村镇银行（借记卡）';
            case 'XYPQZYCB_DEBIT':
                return '信阳平桥中原村镇银行（借记卡）';
            case 'XZB_DEBIT':
                return '西藏银行（借记卡）';
            case 'YACCB_DEBIT':
                return '雅安市商业银行（借记卡）';
            case 'YBCCB_DEBIT':
                return '宜宾商业银行（借记卡）';
            case 'YKCB_DEBIT':
                return '营口沿海银行（借记卡）';
            case 'YLB_DEBIT':
                return '亿联银行（借记卡）';
            case 'YNHTB_CREDIT':
                return '云南红塔银行（信用卡）';
            case 'YNHTB_DEBIT':
                return '云南红塔银行（借记卡）';
            case 'YNRCCB_CREDIT':
                return '云南农信（信用卡）';
            case 'YNRCCB_DEBIT':
                return '云南农信（借记卡）';
            case 'YQCCB_DEBIT':
                return '阳泉市商业银行（借记卡）';
            case 'YQMYRB_DEBIT':
                return '玉泉蒙银村镇银行（借记卡）';
            case 'YRRCB_CREDIT':
                return '黄河农商银行（信用卡）';
            case 'YRRCB_DEBIT':
                return '黄河农商银行（借记卡）';
            case 'YTB_DEBIT':
                return '烟台银行（借记卡）';
            case 'YYBSCB_DEBIT':
                return '沂源博商村镇银行（借记卡）';
            case 'ZCRB_DEBIT':
                return '遵义新蒲长征村镇银行（借记卡）';
            case 'ZGB_DEBIT':
                return '自贡银行（借记卡）';
            case 'ZGCB_DEBIT':
                return '北京中关村银行（借记卡）';
            case 'ZHCB_DEBIT':
                return '庄河汇通村镇银行（借记卡）';
            case 'ZHQYTB_DEBIT':
                return '沾化青云村镇银行（借记卡）';
            case 'ZJB_DEBIT':
                return '紫金农商银行（借记卡）';
            case 'ZJLXRB_DEBIT':
                return '兰溪越商银行（借记卡）';
            case 'ZJRCUB_CREDIT':
                return '浙江农信（信用卡）';
            case 'ZJRCUB_DEBIT':
                return '浙江农信（借记卡）';
            case 'ZJTLCB_CREDIT':
                return '浙江泰隆银行（信用卡）';
            case 'ZJTLCB_DEBIT':
                return '浙江泰隆银行（借记卡）';
            case 'ZRCB_CREDIT':
                return '张家港农商行（信用卡）';
            case 'ZRCB_DEBIT':
                return '张家港农商行（借记卡）';
            case 'ZSXKCCB_DEBIT':
                return '中山小榄村镇银行（借记卡）';
            case 'ZYB_CREDIT':
                return '中原银行（信用卡）';
            case 'ZYB_DEBIT':
                return '中原银行（借记卡）';
            case 'ZZB_CREDIT':
                return '郑州银行（信用卡）';
            case 'ZZB_DEBIT':
                return '郑州银行（借记卡）';
            case 'ZZCCB_DEBIT':
                return '枣庄银行（借记卡）';
            case 'DINERSCLUD':
                return 'DINERSCLUD';
            case 'MASTERCARD':
                return 'MASTERCARD';
            case 'VISA':
                return 'VISA';
            case 'AMERICANEXPRESS':
                return 'AMERICANEXPRESS';
            case 'DISCOVER':
                return 'DISCOVER';
            case 'OTHERS':
                return '其他（银行卡以外）';
            default:
                return '未知银行';
        }
    }

    /**
     * 交易类型对照表
     *
     * @since 1.0.0
     * @param string $trade_type 交易类型
     */
    static function trade($trade_type)
    {
        $trade_type = strtoupper($trade_type);

        switch ($trade_type) {
            case 'JSAPI':
                return '公众号支付';
            case 'APP':
                return 'APP 支付';
            case 'NATIVE':
                return '扫码支付';
            case 'MICROPAY':
                return '付款码支付';
            case 'MWEB':
                return 'H5 支付';
            case 'FACEPAY':
                return '刷脸支付';
            default:
                return '未知支付方式';
        }
    }

    /**
     * 交易状态对照表
     *
     * @since 1.0.0
     * @param string $status 交易状态别名
     */
    static function status($status)
    {
        $status = strtoupper($status);

        switch ($status) {
            case 'CANCEL':
                return '已取消';
            case 'SUCCESS':
                return '支付成功';
            case 'REFUND':
                return '已退款';
            case 'NOTPAY':
                return '未支付';
            case 'CLOSED':
                return '已关闭';
            case 'REVOKED':
                return '已撤销';
            case 'USERPAYING':
                return '支付中';
            case 'PAYERROR':
                return '支付失败';
            case 'ACCEPT':
                return '等待扣款';
            default:
                return '未知状态';
        }
    }
}

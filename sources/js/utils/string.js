/**
 * 字符串处理函数
 *
 * @since 1.0.0
 * - count          字数统计
 * - clipboard      内容复制到剪切板
 * - random         生成随机字符串
 * - removehtml  移除 HTML 标签
 * - numberonly     保留数字
 */
export default class string {
    /**
     * 字数统计
     *
     * @since 1.0.0
     * @param string str 需要统计的字符集
     */
    static count = (str) => {
        str = String(str)
        let r = 0
        for (let i = 0; i < str.length; i++) {
            let c = str.charCodeAt(i)
            // Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
            // Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
            if (
                (c >= 0x0 && c < 0x81) ||
                c == 0xf8f0 ||
                (c >= 0xff61 && c < 0xffa0) ||
                (c >= 0xf8f1 && c < 0xf8f4)
            ) {
                r += 1
            } else {
                r += 2
            }
        }
        return r
    }

    /**
     * 内容复制到剪切板
     *
     * @since 1.0.0
     * @param string text 需要复制的内容
     */
    static clipboard = (text) => {
        navigator.clipboard.writeText(text)
    }

    /**
     * 生成随机字符串
     *
     * @since 1.0.0
     * @param integer num 生成的字符串数，默认：32位
     */
    static random = (num) => {
        num = num || 32
        let t = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz1234567890',
            a = t.length,
            n = ''
        for (let i = 0; i < num; i++)
            n += t.charAt(Math.floor(Math.random() * a))
        return n
    }

    /**
     * 移除 HTML 标签
     *
     * @since 1.0.0
     */
    static removehtml = (string) => {
        return string.replace(/<[^>]+>/g, '')
    }

    /**
     * 移除其他字符，只保留数值
     *
     * @since 1.0.0
     */
    static numberonly = (string) => {
        return string.replace(/[^\d.]/g, '')
    }

    /**
     * 密码强度检测
     * 使用密码等级进行判断，初始值为 0，包含字母或数字加 1，小于 6 位数减 1
     * 根据返回数值进行强度判断
     *
     * @since 1.0.0
     * @param string value  需要检测的密码
     */
    static checkpass = (value) => {
        let i = 0

        // 判断密码小于 6 个字符
        if (6 > value.length) {
            return i
        }

        // 判断包含字母
        if (value.match(/[a-z]/gi)) {
            i++
        }

        // 判断包含数字
        if (value.match(/[0-9]/gi)) {
            i++
        }

        // 判断包含数字和字母
        if (value.match(/(.[^a-z0-9])/gi)) {
            i++
        }

        // 判断小于6个字符
        if (value.length < 6 && i > 0) {
            i--
        }

        return i
    }

    /**
     * 统计对象包含元素个数
     *
     * @since 1.0.0
     * @param object o 需要统计的对象
     */
    static countobject = (o) => {
        var t = typeof o
        if (t == 'string') {
            return o.length
        } else if (t == 'object') {
            var n = 0
            for (var i in o) {
                n++
            }
            return n
        }
        return false
    }

    /**
     * 数字金额转中文大写
     *
     * @since 1.0.0
     * @param integer money
     */
    static changemoney = (money) => {
        var cnNums = new Array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'); //汉字的数字
        var cnIntRadice = new Array('', '拾', '佰', '仟'); //基本单位
        var cnIntUnits = new Array('', '万', '亿', '兆'); //对应整数部分扩展单位
        var cnDecUnits = new Array('角', '分', '毫', '厘'); //对应小数部分单位
        var cnInteger = '整'; //整数金额时后面跟的字符
        var cnIntLast = '元'; //整型完以后的单位
        var maxNum = 999999999999999.9999; //最大处理的数字
        var IntegerNum; //金额整数部分
        var DecimalNum; //金额小数部分
        var ChineseStr = ''; //输出的中文金额字符串
        var parts; //分离金额后用的数组，预定义
        var Symbol = ''; //正负值标记
        if ('' == money) {
            return;
        }

        money = parseFloat(money);
        if (money >= maxNum) {
            alert('超出最大处理数字');
            return "";
        }
        if (money == 0) {
            ChineseStr = cnNums[0] + cnIntLast + cnInteger;
            return ChineseStr;
        }
        if (money < 0) {
            money = -money;
            Symbol = '负 ';
        }
        money = money.toString(); //转换为字符串
        if (money.indexOf('.') == -1) {
            IntegerNum = money;
            DecimalNum = '';
        } else {
            parts = money.split('.');
            IntegerNum = parts[0];
            DecimalNum = parts[1].substr(0, 4);
        }
        if (parseInt(IntegerNum, 10) > 0) { //获取整型部分转换
            var zeroCount = 0;
            var IntLen = IntegerNum.length;
            for (var i = 0; i < IntLen; i++) {
                var n = IntegerNum.substr(i, 1);
                var p = IntLen - i - 1;
                var q = p / 4;
                var m = p % 4;
                if (n == '0') {
                    zeroCount++;
                } else {
                    if (zeroCount > 0) {
                        ChineseStr += cnNums[0];
                    }
                    zeroCount = 0; //归零
                    ChineseStr += cnNums[parseInt(n)] + cnIntRadice[m];
                }
                if (m == 0 && zeroCount < 4) {
                    ChineseStr += cnIntUnits[q];
                }
            }
            ChineseStr += cnIntLast;
            //整型部分处理完毕
        }
        if (DecimalNum != '') { //小数部分
            var decLen = DecimalNum.length;
            for (var i = 0; i < decLen; i++) {
                var n = DecimalNum.substr(i, 1);
                if (n != '0') {
                    ChineseStr += cnNums[Number(n)] + cnDecUnits[i];
                }
            }
        }
        if (ChineseStr == '') {
            ChineseStr += cnNums[0] + cnIntLast + cnInteger;
        } else if (DecimalNum == '') {
            ChineseStr += cnInteger;
        }
        ChineseStr = Symbol + ChineseStr;

        return ChineseStr;
    }
}

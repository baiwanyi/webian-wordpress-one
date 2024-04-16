/**
 * 二维码生成类
 *
 * @since 1.0.0
 * @see https://www.npmjs.com/package/qrcode
 */

/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import QRCode from 'qrcode';

export default class qrcode {

    /**
     * 二维码生成默认选项
     *
     * @since 1.0.0
     * @param string     errorCorrectionLevel  纠错级别，可设定值 ：low, medium, quartile, high, L, M, Q, H。默认：M
     * @param integer    margin                空白边距宽度，默认：2
     * @param integer    width                 输出图像宽度。默认：320（px）
     * @param string     color.dark            深色模块的颜色。值必须采用十六进制格式 （RGBA）。默认：#000000FF
     * @param string     color.light           亮模块的颜色。值必须采用十六进制格式 （RGBA）。默认：#FFFFFFFF
     */
    static defaults = {
        errorCorrectionLevel: 'M',
        margin: 2,
        width: 320,
        color: {
            dark: '#000000FF',
            light: '#FFFFFFFF'
        }
    }

    /**
     * 绘制画布
     *
     * @since 1.0.0
     * @param string wrapper    渲染目标元素 ID
     * @param string content    需要生成二维码的内容
     * @param object options    二维码生成选项
     */
    static canvas = (wrapper, content, options) => {

        /** 设定参数默认值 */
        var options = $.extend(this.defaults, options);

        QRCode.toCanvas(content, options, function (error, canvas) {
            if (error) return;
            jQuery(wrapper).html(canvas)
        })
    }

    /**
     * 绘制 Base64 格式
     *
     * @since 1.0.0
     * @param string wrapper    渲染目标元素 ID
     * @param string content    需要生成二维码的内容
     * @param object options    二维码生成选项
     */
    static base64 = (wrapper, content, options) => {

        /** 设定参数默认值 */
        var options = $.extend(this.defaults, options);

        QRCode.toDataURL(content, options, function (error, url) {
            if (error) return;
            jQuery(wrapper).attr('src', url)
        })
    }
}

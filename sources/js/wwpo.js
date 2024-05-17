/**
 * WWPO 有用的 DOM 操作
 *
 * @since 1.0.0
 */

/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import base64 from './utils/base64'
import event from './utils/event'
import pagination from './utils/pagination'
import qrcode from './utils/qrcode'
import scroll from './utils/scroll'
import string from './utils/string'
import toast from './utils/toast'
import url from './utils/url'

/**
 * ------------------------------------------------------------------------
 * 暴露命名空间
 * ------------------------------------------------------------------------
 */
export default class wwpo {

    /**
     * ------------------------------------------------------------------------
     * Base64 应用函数
     * ------------------------------------------------------------------------
     */
    static base64 = base64

    /**
     * ------------------------------------------------------------------------
     * 字符串应用函数
     * ------------------------------------------------------------------------
     */
    static string = string

    /**
     * ------------------------------------------------------------------------
     * URL 应用函数
     * ------------------------------------------------------------------------
     */
    static url = url

    /**
     * ------------------------------------------------------------------------
     * 模版应用函数
     * ------------------------------------------------------------------------
     */
    /**
     * 在元素之后插入模版内容
     *
     * @since 1.0.0
     */
    static after = (div, template_id, data) => {
        jQuery(div).after(this.load(template_id, data))
    }

    /**
     * 在元素的结尾插入模版内容
     *
     * @since 1.0.0
     */
    static append = (div, template_id, data) => {
        jQuery(div).append(this.load(template_id, data))
    }

    /**
     * 在元素之前插入模版内容
     *
     * @since 1.0.0
     */
    static before = (div, template_id, data) => {
        jQuery(div).before(this.load(template_id, data))
    }

    /**
     * 使用模版内容覆盖元素
     *
     * @since 1.0.0
     */
    static html = (div, template_id, data) => {
        jQuery(div).html(this.load(template_id, data))
    }

    /**
     * 渲染加载模版内容
     *
     * @since 1.0.0
     * @param string template_id    页面模板 ID
     * @param object data           内容数据
     *
     * 操作步骤：
     * - 获取模板内容
     * - 使用传入数据格式化显示内容数据
     */
    static load = (template_id, data) => {
        let template = jQuery('#tmpl-' + template_id).html()
        return this.template(template, data)
    }

    /**
     * 在元素的开头插入模版内容
     *
     * @since 1.0.0
     */
    static prepend = (div, template_id, data) => {
        jQuery(div).prepend(this.load(template_id, data))
    }

    /**
     * 设定 Lodash 模版应用
     *
     * @since 1.0.0
     * @param string template    模版内容
     * @param object data        内容数据，模版使用 data.{key} 进行数据调用
     * @see https://www.lodashjs.com/
     */
    static template = (template, data) => {
        data = data || {}
        let compiled,
            format = {
                evaluate: /<%([\s\S]+?)%>/g,
                interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                variable: 'data',
            }

        compiled = compiled || _.template(template, format)
        return compiled(data)
    }

    /**
     * ------------------------------------------------------------------------
     * jQuery 事件
     * ------------------------------------------------------------------------
     */
    /**
     * 变更事件
     *
     * @since 1.0.0
     * @param string    current     触发事件的 DOM 元素
     * @param function  callback    回调函数
     */
    static change = (current, callback) => {
        event.on('change', current, callback)
    }

    /**
     * 点击事件
     *
     * @since 1.0.0
     * @param string    current     触发事件的 DOM 元素
     * @param function  callback    回调函数
     */
    static click = (current, callback) => {
        event.on('click', current, callback)
    }

    /**
     * 焦点事件
     *
     * @since 1.0.0
     * @param string    current     触发事件的 DOM 元素
     * @param function  callback    回调函数
     */
    static focus = (current, callback) => {
        event.on('focus', current, callback)
    }

    /**
     * 输入事件
     *
     * @since 1.0.0
     * @param string    current     触发事件的 DOM 元素
     * @param function  callback    回调函数
     */
    static input = (current, callback) => {
        event.on('input propertychange', current, callback)
    }

    /**
     * 获取数组格式的表单内容
     *
     * @since 1.0.0
     * @param string t 需要读取的表单 ID
     */
    static form(t, j) {
        j = j || {}
        let obj = jQuery(t).serializeArray()
        jQuery.each(obj, function () {
            if (j[this.name]) {
                if (!j[this.name].push) {
                    j[this.name] = [j[this.name]]
                }
                j[this.name].push(this.value || '')
                j[this.name] = j[this.name].join(',')
            } else {
                j[this.name] = this.value || ''
            }
        })
        return j
    }

    /**
     * 初始化加载事件
     *
     * @since 1.0.0
     * @param function callback 回调函数
     */
    static ready = (callback) => {
        jQuery(() => {
            /**
             * 绑定回车提交表单
             *
             * @since 1.0.0
             */
            jQuery(document).on('keypress', (event) => {
                if (0 == jQuery('button[submited]').length) {
                    return
                }

                if ('Enter' == event.key) {
                    jQuery('button[submited]').click()
                    return false
                }
            })

            // 全局加载函数
            if ('function' === typeof callback) {
                callback()
            }
        })
    }

    /**
     * 触发 jQuery.scroll 事件
     *
     * @since 1.0.0
     * @param string current    触发事件的 DOM 元素，默认：document
     * @param function callback 回调函数
     */
    static scroll = (options) => {
        scroll.now(options)
    }

    /**
     * 监听滚动到底部
     *
     * @since 1.0.0
     * @param objcet options 选项设置
     */
    static bottom = (options) => {
        scroll.bottom(options)
    }

    /**
     * 下一页分页
     *
     * @since 1.0.0
     * @param integer   paged       当前页码
     * @param integer   maxpaged    最大页码
     * @param string    action      加载内容 AJAX 动作
     */
    static nextpaged = (paged, maxpaged, action) => {
        return pagination.next(paged, maxpaged, action)
    }

    /**
     * 页码分页
     *
     * @since 1.0.0
     * @param integer   paged       当前页码
     * @param integer   maxpaged    最大页码
     * @param string    action      加载内容 AJAX 动作
     */
    static paged = (paged, maxpaged, action) => {
        return pagination.paged(paged, maxpaged, action)
    }

    /**
     * ------------------------------------------------------------------------
     * 二维码应用函数
     * ------------------------------------------------------------------------
     */
    /**
     * 绘制画布二维码
     *
     * @since 1.0.0
     * @param string div        显示的 DOM 元素
     * @param string content    需要生成二维码的内容
     * @param object options    二维码生成选项
     */
    static qrcode = (div, content, options) => {
        qrcode.canvas(div, content, options)
    }

    /**
     * 绘制 Base64 格式二维码
     *
     * @since 1.0.0
     * @param string div        显示的 DOM 元素
     * @param string content    需要生成二维码的内容
     * @param object options    二维码生成选项
     */
    static qrcode64 = (div, content, options) => {
        qrcode.base64(div, content, options)
    }

    /**
     * 提示框
     *
     * @since 1.0.0
     * @param string    icon    图标
     * @param string    text    消息文本
     * @param boolean   close   是否自动关闭，默认：false
     */
    static toast = (icon, text, close) => {
        return toast(icon, text, close)
    }
}

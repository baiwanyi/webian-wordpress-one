/**
 * 滚动监听
 *
 * @since 1.0.0
 */
export default class scroll {
    /**
     * 滚动到底部
     *
     * @since 1.0.0
     * @param object options
     * {
     *  参数选项
     *  @var string     div     监听目标，默认：document
     *  @var integer    bottom  滚动到底部的距离，默认：0
     *  @var function   success 回调函数
     * }
     */
    static bottom = (options) => {
        var defaults = {
            div: '',
            bottom: 0,
            success: '',
        }

        /** 设定参数默认值 */
        var options = $.extend(defaults, options)

        // 设定高度默认值
        if (options.div) {
            var _this_body = jQuery(options.div)
        } else {
            var _this_body = jQuery(document)
        }

        /** 监听滚动开始 */
        _this_body.on('scroll', function () {
            let _this = jQuery(this)
            let _body_height = _this.height() // 当前元素的显示高度
            let _body_scroll_top = _this.scrollTop() // 获取此刻滚动位置与顶部的距离
            let _window_height = jQuery(window).height() // 当前元素的页面高度

            if (jQuery('body').hasClass('wwpo-autoload')) {
                return
            }

            /** 判断页面高度减去滚动搞点的差小于或等于显示高度，执行函数 */
            if (
                _body_height <=
                Math.ceil(_window_height + _body_scroll_top) + options.bottom
            ) {
                jQuery('body').addClass('wwpo-autoload');

                /** 判断执行函数 */
                if ('function' == typeof options.success) {
                    options.success()
                }
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
    static now = (options) => {
        var defaults = {
            main: null,
            ajaxurl: '',
            div: '',
            data: {},
            bottom: 0,
            beforesend: '',
            success: '',
        }

        /** 设定参数默认值 */
        var options = $.extend(defaults, options);

        if (options.main) {
            options.data = jQuery(options.main).data()
        }

        options.data['nonce'] = wwpoSettings.nonce

        scroll.bottom({
            div: options.div,
            bottom: options.bottom,
            success: () => {
                wwpo.ajax(null, {
                    method: 'GET',
                    ajax: options.ajaxurl,
                    data: options.data,
                    login: 'wp',
                    beforesend: () => {

                        jQuery('body').addClass('wwpo-autoload');

                        if ('function' != typeof options.beforesend) {
                            return
                        }

                        options.beforesend()
                    },
                    success: (result, textStatus, xhr) => {

                        if ('function' != typeof options.success) {
                            return
                        }

                        let success = options.success(result, textStatus, xhr);

                        if (false === success) {
                            jQuery('body').addClass('wwpo-autoload');
                            return
                        }

                        jQuery('body').removeClass('wwpo-autoload');
                        scroll.now(options)
                    }
                })
            }
        })
    }
}

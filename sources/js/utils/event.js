/**
 * WWPO 事件
 *
 * @since 1.0.0
 */

export default class event {

    /**
     * ON 事件
     *
     * @since 1.0.0
     * @param string    action      绑定动作名称
     * @param string    current     操作事件的 DOM 元素
     * @param function  callback    回调函数
     */
    static on = (action, current, callback) => {
        jQuery(document).on(action, current, function (event) {

            if (['A', 'AREA'].includes(this.tagName)) {
                event.preventDefault()
            }

            let _this = null;

            if (null != current) {
                _this = jQuery(this);
            }

            if ('function' === typeof callback) {
                callback(_this)
            }
        })
    }
}

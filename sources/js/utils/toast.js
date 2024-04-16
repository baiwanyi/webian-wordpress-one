/**
 * 消息反馈层
 *
 * @since 1.0.0
 * @param string    icon    图标
 * @param string    text    提示信息
 * @param boolean   close   自动关闭
 */
var toast = (icon, text, close) => {
    // 显示图标默认值
    icon = icon || 'success'

    // 自动关闭默认值
    close = close || false

    // 反馈层参数
    let _toasts = '#wwpo-toasts'
    let _data = {
        title: text || '',
    }

    // 判断反馈层
    if (jQuery(_toasts).length) {
        jQuery(_toasts).remove()
    }

    /** 判断使用图标为加载动画 */
    if ('loading' == icon) {
        _data['icon'] =
            '<i class="spinner-border" style="width: 3rem; height: 3rem;"></i>'
    } else {
        _data['icon'] =
            '<svg viewBox="0 0 1479 1024" xmlns="http://www.w3.org/2000/svg"><path d="M1401.287 0l77.824 77.824-934.172 934.23L0 467.113l136.249-136.248L583.85 622.763 1401.287 0z" fill="#fff"/></svg>'
    }

    // 显示反馈信息内容
    jQuery('body').append(wwpo.template('<div class="position-fixed start-50 translate-middle bg-dark bg-opacity-75 rounded-3 p-3 text-light d-flex justify-content-center flex-column align-items-center" id="wwpo-toasts" role="toasts" style="top: 40%; width: 10rem; height: 10rem; z-index: 992048;"><div class="d-flex justify-content-center align-items-center my-3 w-50">{{{data.icon}}}</div><div style="font-size: 1.25rem;">{{data.title}}</div></div>', _data))

    // 设定显示动画
    jQuery(_toasts).animate({ opacity: 1 }, 300)

    /** 判断自动关闭，加载隐藏消函数 */
    if (close) {
        setTimeout(() => {
            jQuery(_toasts).animate({ opacity: 0 }, 500, () => {
                jQuery(_toasts).remove()
            })
        }, 1000)
    }
}

export default toast

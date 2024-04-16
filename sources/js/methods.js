/**
 * wwpo 有用的 DOM 操作组件
 *
 * @since 1.0
 */

/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import wwpo from './wwpo'

/**
 * 返回上一页链接
 *
 * @since 1.0.0
 */
wwpo.click('a[rel="back"], [data-wwpo-action="back"]', () => {
    window.history.back()
})

/**
 * 转跳到页面指定描点位置（A 标签动作）
 *
 * @since 1.0.0
 */
wwpo.click('a[rel="anchor"]', (current) => {
    let _href = current.attr('href')
    let _scrollTop = jQuery(_href).offset().top - 50
    jQuery('body,html').animate({ scrollTop: _scrollTop }, 300)
})

/**
 * 转跳到页面指定描点位置（按钮动作）
 *
 * @since 1.0.0
 */
wwpo.click('[data-wwpo-target]', (current) => {
    let _href = current.data('wwpoTarget')
    let _scrollTop = jQuery(_href).offset().top - 50

    // 转跳到页面顶部
    if ('top' == _href) {
        _scrollTop = 0
    }

    jQuery('body,html').animate({ scrollTop: _scrollTop }, 300)
})

/**
 * 关闭页面窗口
 *
 * @since 1.0.0
 */
wwpo.click('a[rel="close"], [data-wwpo-action="close"]', () => {
    window.close()
    WeixinJSBridge.call('closeWindow')
})

/**
 * 刷新当前页面
 *
 * @since 1.0.0
 */
wwpo.click('a[rel="refresh"], [data-wwpo-action="refresh"]', () => {
    window.location.reload()
    $('body, html').scrollTop(0)
})

/**
 * 转跳到指定页面链接
 *
 * @since 1.0.0
 */
wwpo.click('[data-wwpo-url]', (current) => {
    let _target = current.data('wwpoUrl')

    if (0 == _target.length) {
        return
    }

    window.location.href = current.data('wwpoUrl')
})

/**
 * 复制按钮值到剪贴板
 *
 * @since 1.0.0
 */
wwpo.click('[data-wwpo-copy]', (current) => {
    let _target = current.data('wwpoCopy')
    wwpo.string.clipboard(_target.html())
    wwpo.toast('success', '复制成功', true)
})

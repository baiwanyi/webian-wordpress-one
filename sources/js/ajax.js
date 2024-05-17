/**
 * 滚动监听
 *
 * @since 1.0.0
 */

/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import wwpo from './wwpo'

const MESSAGE_ID = '#message'


wwpo.click('[data-wwpo-ajax]', (current) => {
    let _data = {}
    let _ajax = current.data('wwpoAjax')
    let _form = current.parents('form')
    let _button_text = current.html()

    if ('undefined' != typeof _form) {
        let fields = _form.serializeArray()

        if (fields.length) {
            jQuery.each(fields, function () {
                _data[this.name] = this.value || ''
            })
        }
    }

    _data['action'] = 'wwpoupdatepost'
    _data['ajax'] = _ajax || ''
    _data['pagenonce'] = wwpoSettings.pagenonce
    _data['pagenow'] = wwpoSettings.pagenow

    if (wwpoSettings.debug) {
        console.log(_data)
    }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: _data,
        beforeSend: () => {

            current.attr('disabled', true)
            current.html('<span class="wwpo-loading"></span>')

            if (jQuery(MESSAGE_ID).length) {
                jQuery(MESSAGE_ID).addClass('hidden').removeClass('error')
                return
            }

            jQuery('hr.wp-header-end').after(
                '<div id="message" class="hidden notice is-dismissible"><p></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button></div>'
            )

        },
        success: (result) => {

            if (wwpoSettings.debug) {
                console.log(result)
            }

            jQuery(MESSAGE_ID).addClass(result.code).removeClass('hidden')
            jQuery(MESSAGE_ID).find('p').html(result.message)

            /** 返回页面顶部 */
            jQuery('html, body').scrollTop(0)
        },
        error: (result) => {

            if (wwpoSettings.debug) {
                console.log(result)
            }

            if ('undefined' != typeof result.responseText) {
                jQuery(MESSAGE_ID).addClass('error').removeClass('hidden')
                jQuery(MESSAGE_ID).find('p').html('<strong>' + result.statusText + '</strong>' + result.responseText)

                /** 返回页面顶部 */
                jQuery('html, body').scrollTop(0)
            }
        },
        complete: () => {
            current.removeAttr('disabled')
            current.html(_button_text)
        }
    })
})

wwpo.click('button.notice-dismiss', () => {
    jQuery(MESSAGE_ID).fadeOut(300, () => {
        jQuery(MESSAGE_ID).remove()
    })
})

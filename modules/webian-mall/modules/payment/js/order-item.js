/**
 * ------------------------------------------------------------------------
 * 后台常量设定
 * ------------------------------------------------------------------------
 */
const ORDER_ITEM_ID = '#wwpo-order-items'
const ORDER_SEARCH_ID = '#wwpo-order-search-items'
const LOADING = '<span class="webui-loading small"></span>'

/**
 * ------------------------------------------------------------------------
 * 加载模板
 * ------------------------------------------------------------------------
 */
import tmpl_display from '../tmpl/order-item-display.html';
import tmpl_display_item from '../tmpl/order-item-display-item.html';
import tmpl_modal from '../tmpl/order-item-search-modal.html';
import tmpl_table from '../tmpl/order-item-search-table.html';

/**
 * ------------------------------------------------------------------------
 * 后台常量设定
 * ------------------------------------------------------------------------
 */
webui.ready(() => {
    if ($('body').hasClass('toplevel_page_wwpo-payment')) {
        orderItem.display()
    }
})

webui.click('[data-action="wpmallorderitem"]', (current) => {
    let _data = current.data()

    switch (current.val()) {

        case 'refresh':
            orderItem.display(true)
            break;

        case 'search':
            orderItem.search()
            break;

        case 'selected':
            orderItem.selected(_data)
            break;

        case 'delete':
            orderItem.delete(_data)
            break;

        case 'modaldelete':
            orderItem.modaldelete(current, _data)
            break;

        case 'modalcreate':
            orderItem.modalcreate(current, _data)
            break;

        default:
            webui.sidebar({
                id: 'orderitem',
                title: '添加产品',
                backdrop: true,
                size: 'end w-75',
                content: tmpl_modal
            })
            break;
    }
});

/**
 *
 */
webui.change('[data-action="wwpoordermodalupdate"]', (current) => {
    let _data = {
        value: current.val(),
        type: current.data('modalType'),
        modal: current.data('modalId'),
        item: current.data('item')
    }

    if (0 == _data.value.length) {
        return
    }

    webui.wp.ajax('POST', current.data('action'), {
        data: _data,
        beforesend: () => {

            if ('name' == _data.type) {
                return
            }

            current.attr('disabled', true)
            current.after('<div id="webui-loading" style="position:absolute;right:0.5rem;top:0.95rem;">' + LOADING + '</div>')
        },
        success: (result) => {

            current.removeAttr('disabled')
            $('#webui-loading').remove()

            if ('undefined' == typeof result.status) {
                return
            }

            if ('name' == _data.type || 'buy' == _data.type) {
                return
            }

            $('#wwpo-order-items-modal-total-' + _data.modal).html(result.data.modal)
            $('#wwpo-order-items-amount').html(result.data.amount)
            $('#wwpo-order-items-total').html(result.data.total)
        }
    })
});

/**
 * 订单产品模块
 *
 * @since 1.0.0
 */
export default class orderItem {

    /**
     * 搜索产品操作
     *
     * @since 1.0.0
     */
    static search = () => {
        let _search_key = $('#wwpo-modal-search-key').val()

        if (0 == _search_key.length) {
            return
        }

        webui.wp.ajax('GET', 'wwpoordersearchitem', {
            data: {
                search: _search_key
            },
            beforesend: () => {
                $(ORDER_SEARCH_ID).find('.noitems').html(LOADING);
            },
            success: (result) => {

                if ('error' == result.status) {
                    $(ORDER_SEARCH_ID).find('.noitems').html(result.message).addClass('text-danger');
                    return
                }

                $(ORDER_SEARCH_ID).html(webui.template(tmpl_table, result.data));
            }
        })
    }

    /**
     * 选择搜索产品操作
     *
     * @since 1.0.0
     */
    static selected = (data) => {
        webui.wp.ajax('POST', 'wwpoorderselecteditem', {
            data: {
                product: data.product,
                order: $('input[name="order_id"]').val()
            },
            beforesend: () => {
                webui.toast('loading')
            },
            success: (result) => {

                $('#webui-toasts').remove()

                if ('error' == result.status) {
                    webui.toast('error', result.message, true)
                    return
                }

                webui.main.sidebar.close('#webui-sidebar-orderitem')

                if (0 == $(ORDER_ITEM_ID + ' div[id^=item]').length) {
                    $(ORDER_ITEM_ID).html(webui.template(tmpl_display, result.data))
                } else {
                    $(ORDER_ITEM_ID).append(webui.template(tmpl_display, result.data))
                }

            }
        })
    }

    /**
     * 删除产品动作
     *
     * @since 1.0.0
     */
    static delete = (data) => {

        webui.wp.ajax('POST', 'wwpoorderdeleteitem', {
            data: {
                item: data.item
            },
            beforesend: () => {
                $('#item-' + data.item).css('background', '#e9686b').fadeOut(500, function () {
                    $(ORDER_ITEM_ID).prepend('<p class="lead">' + LOADING + '</p>')
                })
            },
            success: (result) => {

                $(ORDER_ITEM_ID).find('p.lead').fadeOut(500, function () {
                    $(this).remove()
                })

                if ('error' == result.status) {
                    $('#item-' + data.item).removeAttr('style').fadeIn(500)
                    return
                }

                $('#item-' + data.item).remove()

                if (0 == $(ORDER_ITEM_ID + ' div[id^=item]').length) {
                    $(ORDER_ITEM_ID).html('<p class="lead">没有相关内容。</p>')
                }
            }
        })
    }

    /**
     *
     * @param {*} data
     */
    static modaldelete = (current, data) => {
        webui.wp.ajax('POST', 'wwpoorderdeletemodal', {
            data: {
                modal: data.modal
            },
            beforesend: () => {
                current.find('.dashicons-before').addClass('hidden')
                current.append(LOADING)
                current.attr('disabled', true)
            },
            success: (result) => {

                current.find('.dashicons-before').removeClass('hidden')
                current.find('.webui-loading').remove()
                current.removeAttr('disabled')

                if ('undefined' == typeof result.status) {
                    return
                }

                $('#item-modal-' + data.modal).css('background', '#e9686b').fadeOut(500, function () {
                    $(this).remove()
                })

                $('#wwpo-order-items-amount').html(result.data.amount)
                $('#wwpo-order-items-total').html(result.data.price_total)
            }
        })
    }

    /**
     *
     * @param {*} data
     */
    static modalcreate = (current, data) => {
        let _item_modal = $('input[name="modalinput"]').val()

        if (0 == _item_modal.length) {
            return
        }

        webui.wp.ajax('POST', 'wwpoordercreatemodal', {
            data: {
                modal: _item_modal,
                item: data.item
            },
            beforesend: () => {
                current.find('.dashicons-before').addClass('hidden')
                current.append(LOADING)
                current.attr('disabled', true)
            },
            success: (result) => {

                current.find('.dashicons-before').removeClass('hidden')
                current.find('.webui-loading').remove()
                current.removeAttr('disabled')

                if ('undefined' == typeof result.status) {
                    return
                }

                $('input[name="modalinput"]').val('')
                $('#wwpo-order-items-table tbody').append(webui.template(tmpl_display_item, result.data))
            }
        })
    }

    /**
     * DELIVERY
     */
    static display = (loading) => {
        webui.wp.ajax('GET', 'wwpoordergetitems', {
            data: {
                order: $('input[name="order_id"]').val()
            },
            beforesend: () => {
                if (loading) {
                    $('div[role="alert"]').remove()
                    $(ORDER_ITEM_ID).html('<p class="lead">' + LOADING + '</p>')
                }
            },
            success: (result) => {
                console.log(result)
                $(ORDER_ITEM_ID).find('p.lead').fadeOut(300, function () {
                    $(this).remove()

                    if ('error' == result.status) {
                        $(ORDER_ITEM_ID).html('<p class="lead">' + result.message + '</p>')
                        return
                    }

                    $(ORDER_ITEM_ID).html(webui.template(tmpl_display, result.data))
                })
            }
        })
    }
}

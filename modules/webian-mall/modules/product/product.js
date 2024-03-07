/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import category from './js/product-category';
import media from './js/product-media';
import post from './js/product-post';
import sku from './js/product-sku';
import './js/terms';

/**
 * ------------------------------------------------------------------------
 * 全局事件
 * ------------------------------------------------------------------------
 */
webui.ready(() => {

    // 编辑页面加载产品规格和库存列表
    if ($('#wpmall-product-price').length) {
        sku.table()
    }

    // 产品素材排序启用函数
    if ($('#wpmall-media-wrapper').length) {
        media.sortable()
    }
});

/**
 * ------------------------------------------------------------------------
 * 产品规格编辑动作
 * ------------------------------------------------------------------------
 */

/**
 * 产品规格编辑点击动作
 */
webui.click('[data-action="wpmallmodal"]', (current) => {
    let action = current.val();

    switch (action) {
        // 新建规格
        case 'create':
            sku.create(current)
            break;
        // 删除规格
        case 'delete':
            sku.delete(current)
            break;
        // 新建项目
        case 'optioncreate':
            sku.optionCreate(current)
            break;
        // 删除项目
        case 'optiondelete':
            sku.optionDelete(current)
            break;
        default:
            break;
    }
});

/**
 * 产品规格名称更新操作
 */
webui.change('div[data-option] input', (current) => {
    sku.optionUpdated(current)
});

/**
 * 产品规格类型选择操作
 */
webui.change('select[data-action="modalselected"]', (current) => {
    sku.selected(current)
});

/**
 * 产品价格库存更新操作
 */
webui.change('tr[data-sku] input', (current) => {
    sku.updated(current)
});

/**
 * ------------------------------------------------------------------------
 * 产品素材编辑动作
 * ------------------------------------------------------------------------
 */

/**
 * ------------------------------------------------------------------------
 * 素材上传动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="mediauploader"]', () => {
    media.uploader()
});

/**
 * ------------------------------------------------------------------------
 * 素材移除动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="mediaremove"]', (current) => {
    media.delete(current)
})

/**
 * ------------------------------------------------------------------------
 * 素材预览动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="mediaview"]', (current) => {
    media.view(current)
})

/**
 * ------------------------------------------------------------------------
 * 批量同步产品库存内容动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="syncsku"]', (current) => {
    switch (current.val()) {
        // 产品条码
        case 'barcode':
            post.syncbarcode()
            break;

        // 产品价格
        case 'price':
            post.syncprice()
            break;

        // 产品库存
        case 'stock':
            post.syncstock()
            break;

        default:
            break;
    }
});

/**
 * ------------------------------------------------------------------------
 * 一键尺码规格动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="modalpost"]', (current) => {
    switch (current.val()) {
        // 新建衣服尺码规格
        case 'size':
            post.createssize()
            break;

        default:
            break;
    }
});

/**
 * ------------------------------------------------------------------------
 * 产品条码查询动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="barcodesearch"]', (current) => {
    let _barcode_id = $('#wpmall-barcode');
    let _product_id = $('input[name="product_id"]').val();
    let _barcode_value = _barcode_id.val();

    current.attr('disabled', true).html('<span class="webui-loading"></span>')
    _barcode_id.removeClass('is-invalid is-valid')
    $('#barcodefeedback').remove()
    $('input#publish').removeAttr('disabled')


    $.post(ajaxurl, { action: 'barcodesearch', barcode: _barcode_value, product_id: _product_id }, (result) => {
        _barcode_id.addClass('is-' + result.code)
        _barcode_id.parent().after('<div id="barcodefeedback" class="text-' + result.css + '">' + result.message + '</div>')

        if ('invalid' == result.code) {
            $('input#publish').attr('disabled', true)
        }

        current.removeAttr('disabled').html('查询')
    })
});

/**
 * ------------------------------------------------------------------------
 * 分类选择动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="selectmenu"]', (current) => {
    let _select_menu = current.parents('.webui-select-menu')
    let _select_content = _select_menu.find('.webui-select-menu__list').html().trim().replace(/\s/g, '')

    if (!_select_content) {
        return
    }

    if (_select_menu.hasClass('show')) {
        current.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2')
        _select_menu.removeClass('show')
    } else {

        if ($('.webui-select-menu').hasClass('show')) {
            $('.webui-select-menu').find('[data-action="selectmenu"]').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2')
            $('.webui-select-menu').removeClass('show')
        }

        current.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2')
        _select_menu.addClass('show')
    }
});

/**
 * ------------------------------------------------------------------------
 * 分类选择动作
 * ------------------------------------------------------------------------
 */
webui.change('input.webui-select-menu__checkbox', (current) => {
    category.checkbox(current)
});

/**
 * ------------------------------------------------------------------------
 * 分类选择动作
 * ------------------------------------------------------------------------
 */
webui.click('button[data-action="removeselectitem"]', (current) => {
    $('.webui-select-menu').find('[data-action="selectmenu"]').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2')
    $('.webui-select-menu').removeClass('show')
    category.remove(current)
});

/**
 * ------------------------------------------------------------------------
 * 分类选择动作
 * ------------------------------------------------------------------------
 */
$(document).click((event) => {

    // 获取点击的区域
    let _target = $(event.target)
    let _parent = _target.parents('.webui-select-menu')

    if (_target.is('.webui-select-menu') && _parent.hasClass('show')) {
        return
    }

    if (false == _target.is('.webui-select-menu') && 0 == _target.closest('.webui-select-menu').length) {

        if ($('.webui-select-menu').hasClass('show')) {
            $('.webui-select-menu').find('[data-action="selectmenu"]').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2')
            $('.webui-select-menu').removeClass('show')
            return
        }
    }
});

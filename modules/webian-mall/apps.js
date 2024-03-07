/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
// npm run wp:build:plugins --path=webian-wp-mall
import './modules/customer/customer';
import './modules/payment/payment';
import './modules/product/product';

/**
 * ------------------------------------------------------------------------
 * 通栏广告文件上传动作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="thumbuploader"]', () => {

    // 设定上传参数
    let _current_uploader_id = $('input[name="thumb_id"]')
    let _uploader_settings = {
        type: 'image',
        selection: _current_uploader_id.val()
    }

    // 设定上传回调函数
    _uploader_settings['callback'] = (attachment) => {
        _current_uploader_id.val(attachment.id);
        $('#wwpo-thumb-figure').remove();
        $('#wwpo-thumb-uploader').after('<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="' + attachment.url + '" class="figure-img img-fluid rounded"></figure>')
    }

    // 调用上传
    webui.wp.uploader(_uploader_settings)
})

/**
 * ------------------------------------------------------------------------
 * 常量设定
 * ------------------------------------------------------------------------
 */
const MEDIA_WRAPPER_ID = '#wpmall-media-wrapper';

/**
 * ------------------------------------------------------------------------
 * 加载模板
 * ------------------------------------------------------------------------
 */
import TMPL_IMAGE from '../html/admin-metabox-image.html';

/**
 * 产品素材操作模块
 *
 * @since 1.0.0
 */
export default class productMedia {

    /**
    * 产品素材上传操作
    */
    static uploader = () => {

        // 设定上传参数
        let _uploader_settings = {
            type: 'image',
            multiple: 1
        }

        let _selection_ids = [];

        // 获取当前列表产品素材 IDs
        $('[name="wpmall_media_ids"]').each(function () {
            _selection_ids.push(Number($(this).val()))
        });

        _uploader_settings['selection'] = _selection_ids;

        // 设定上传回调函数
        _uploader_settings['callback'] = (attachment) => {

            let _image_display_data = {}

            // 遍历选择媒体文件
            $.each(attachment, function (i, item) {

                // 跳过 ID 为 0 的项目（3个以上会有空数据产生）
                if (0 == item.id) {
                    return true
                }

                // 设定显示列表
                _image_display_data[item.id] = {
                    id: item.id,
                    sort: item.menuOrder,
                    url: item.url.replace('original', 'large')
                }
            });

            $(MEDIA_WRAPPER_ID).html(webui.template(TMPL_IMAGE, _image_display_data))
        }

        // 调用上传
        webui.wp.uploader(_uploader_settings)
    }

    /**
     * 产品素材删除操作
     */
    static delete = (current) => {
        let _media_id = current.val();

        // 删除动画，移除作品
        $('#post-' + _media_id + ' .card').css('background', '#e9686b');
        $('#post-' + _media_id).fadeOut(500, () => {
            $('#post-' + _media_id).remove()
        })
    }

    /**
     * 产品素材预览操作
     */
    static view = (current) => {
        webui.modal({
            title: '图片预览',
            size: 'fullscreen',
            content: '<div class="thumb-view">' + current.html() + '</div>'
        })
    }

    /**
     * 产品素材列表拖动排序
     */
    static sortable = () => {
        jQuery(MEDIA_WRAPPER_ID).sortable({
            containment: 'parent',
            cursor: 'move',
            revert: true
        });
        jQuery(MEDIA_WRAPPER_ID).disableSelection()
    }
}

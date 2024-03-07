/**
 * ------------------------------------------------------------------------
 * 分类目录排序操作
 * ------------------------------------------------------------------------
 */
if ('product_category' == $('input[name="taxonomy"]').val()) {
    jQuery('#the-list').sortable({
        containment: 'parent',
        connectWith: "#the-list",
        handle: '.column-medium-sort',
        axis: 'y',
        cursor: 'move',
        revert: true,
        update: () => {

            // 设定传参数组
            let _data = {
                action: 'category_order_updated',
                term_ids: {}
            }

            // 遍历排序表单，获取分类目录 ID
            $('input[name^="term_ids"]').each(function (i) {
                _data.term_ids[i] = $(this).data('term')
            })

            // AJAX 更新排序内容
            jQuery.post(ajaxurl, _data, function (result) {

                // 弹窗提示
                webui.toast(result.icon, result.title, true)

                // 设定排序序号
                $.each(_data.term_ids, function (i, term_id) {
                    let index = Number(i) + 1
                    $('[data-term="' + term_id + '"]').val(index)
                })
            })
        }
    }).disableSelection()
}

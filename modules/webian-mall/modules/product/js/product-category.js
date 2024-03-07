/**
 * ------------------------------------------------------------------------
 * 加载模板
 * ------------------------------------------------------------------------
 */
import TMPL_MENU_OPTION from '../html/category-menu-option.html';
import TMPL_MENU_CONTENT from '../html/category-menu-content.html';

/**
 * 产品内容规格编辑操作模块
 *
 * @since 1.0.0
 */
export default class productCategory {

    static checkbox = (current) => {

        let _input_id = current.attr('id')
        let _input_name = current.attr('name').replace(/wpmall-|\[\]/gi, '')
        let _input_value = current.val()
        let _input_parent = current.data('parent')
        let _label_id = $('label[for="' + _input_id + '"]')
        let _label_title = _label_id.text()

        // 获取分类目录 JSON 值
        let _category_data = $('#wpmall-product-category-data').val()

        // 转换成对象
        _category_data = JSON.parse(_category_data)

        if (current.prop('checked')) {


            if ('category' == _input_name) {
                let _parent_name = _category_data['parent'][_input_parent] || ''
                console.log(_parent_name)
                if (_parent_name) {
                    _label_title = _parent_name + ' - ' + _label_title
                }
            }

            $('#webui-select-menu-' + _input_name + '-content').append(webui.template(TMPL_MENU_CONTENT, {
                title: _label_title,
                type: _input_name,
                slug: _input_value
            }))
        } else {
            $('button[value="' + _input_value + '"]').remove()
        }

        if ('category' == _input_name) {
            productCategory.option()
        }
    }

    static remove = (current) => {
        let _input_value = current.val()
        let _input_type = current.data('type')
        let _input_id = $('#webui-checkbox-' + _input_value)
        _input_id.removeAttr('checked')
        current.remove()

        if ('category' == _input_type) {
            productCategory.option()
        }
    }

    static option = () => {

        // 获取分类目录 JSON 值
        let _category_data = $('#wpmall-product-category-data').val()

        // 转换成对象
        _category_data = JSON.parse(_category_data)

        let _current_tags = {}
        let _current_brand = {}

        if (!_.isEmpty(_category_data.tags)) {
            _.each(_category_data.tags, function (tags_name, tags_slug) {
                _current_tags[tags_slug] = {
                    type: 'tags',
                    name: tags_name
                }
            })
        }

        $('input[name="wpmall-category[]"]:checked').each(function () {
            let _parent_id = $(this).data('parent')

            _.each(_category_data.category, function (category) {
                if (_parent_id == category.id) {

                    _.each(category.brand, function (brand_name, brand_slug) {
                        _current_brand[brand_slug] = {
                            type: 'brand',
                            name: brand_name
                        }
                    })

                    _.each(category.tags, function (tags_name, tags_slug) {
                        _current_tags[tags_slug] = {
                            type: 'tags',
                            name: tags_name
                        }
                    })
                }
            })
        })

        $('#webui-select-menu-brand .webui-select-menu__list').html(webui.template(TMPL_MENU_OPTION, _current_brand))
        $('#webui-select-menu-tags .webui-select-menu__list').html(webui.template(TMPL_MENU_OPTION, _current_tags))
        $('#webui-select-menu-brand-content').empty()
        $('#webui-select-menu-tags-content').empty()
    }
}

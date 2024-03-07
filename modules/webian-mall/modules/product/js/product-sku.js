/**
 * ------------------------------------------------------------------------
 * 加载模板
 * ------------------------------------------------------------------------
 */
import TMPL_MODAL from '../html/admin-metabox-modal.html';
import TMPL_PRICE from '../html/admin-metabox-price.html';

/**
 * ------------------------------------------------------------------------
 * 常量
 * ------------------------------------------------------------------------
 */
const MODAL_DATA_ID = '#wpmall-modal-data';
const MODAL_LIST_ID = '[id*="wpmall-modal"]';
const MODAL_OPTION_ID = '[id*="wpmall-modal-option"]';
const PRICE_DATA_ID = '#wpmall-price-data';
const MODAL_DEFAULT_SKU = { stock: '', buy: '', sale: '', code: '', vip: '' };

/**
 * 产品 SKU 规格编辑操作模块
 *
 * @since 1.0.0
 */
export default class productSku {

    /**
     * 新建产品规格
     *
     * @param object current
     */
    static create = (current) => {

        // 设定规格 ID（随机 6 位数）
        let _modal_id = Math.ceil(Math.random() * 1000000);
        let _modal_data = $(MODAL_DATA_ID).val() || {};

        // 判断规格数据，转换成 JSON 对象
        if (_modal_data.length && 'object' != typeof _modal_data) {
            _modal_data = JSON.parse(_modal_data)
        }

        // 判断规格数量为 2 个时禁止以下操作
        if (2 == _.size(_modal_data)) {
            return
        }

        // 设置新建规格数据
        _modal_data[_modal_id] = {
            id: _modal_id,
            type: 0,
            list: {},
            order: 0
        };

        // 判断规格数量为 2 个时，禁用新建按钮，并设置当前规格排序为 1
        if (2 == _.size(_modal_data)) {
            current.attr('disabled', true)
            _modal_data[_modal_id]['order'] = 1;
        }

        // 写入数据保存表单
        // 由于新建规格会更改关联 SKU 名称，为保证不生成冗余数据，因此需要清空价格库存数据
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));
        $(PRICE_DATA_ID).empty();

        // 生成规格列表和价格库存表格
        productSku.table(_modal_data);
    }

    /**
     * 删除产品规格
     *
     * @param object current
     */
    static delete = (current) => {

        // 获取规格 ID
        let _modal_id = current.parents(MODAL_LIST_ID).data('modal');
        let _modal_data = $(MODAL_DATA_ID).val() || {};
        let _price_data = $(PRICE_DATA_ID).val() || {};

        // 判断规格数据，转换成 JSON 对象
        if (_modal_data.length && 'object' != typeof _modal_data) {
            _modal_data = JSON.parse(_modal_data)
        }

        // 判断库存价格数据，转换成 JSON 对象
        if (_price_data.length && 'object' != typeof _price_data) {
            _price_data = JSON.parse(_price_data)
        }

        // 删除当前规格 ID 的数据内容
        delete _modal_data[_modal_id];

        // 判断规格数量小于 2 个时，解除新建按钮的禁用状态，并设置数据内容的规格排序为 0
        if (2 > _.size(_modal_data)) {
            $('[data-action="modalnew"]').removeAttr('disabled');

            $.each(_modal_data, function (modal_id) {
                _modal_data[modal_id]['order'] = 0;
            });
        }

        // 遍历价格库存数据，删除包括当前规格 ID 的数据内容
        $.each(_price_data, function (key) {
            if (-1 !== key.indexOf(_modal_id)) {
                delete _price_data[key];
            }
        });

        // 写入数据保存表单
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));
        $(PRICE_DATA_ID).val(JSON.stringify(_price_data));

        // 生成规格列表和价格库存表格
        productSku.table(_modal_data);
    }

    /**
     * 选择产品规格类型
     *
     * @param object current
     */
    static selected = (current) => {

        // 获取规格 ID
        let _modal_id = current.parents(MODAL_LIST_ID).data('modal');
        let _modal_data = $(MODAL_DATA_ID).val();

        // 规格数据转换成 JSON 对象
        _modal_data = JSON.parse(_modal_data);

        // 设定当前规格 ID 数据的规格类型
        _modal_data[_modal_id]['type'] = current.val();

        // 写入数据保存表单
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));
    }

    /**
     * 新建产品规格项目
     *
     * @param object current
     */
    static optionCreate = (current) => {

        let _modal_id = current.parents(MODAL_LIST_ID).data('modal');   // 获取规格 ID
        let _option_id = String(Math.ceil(Math.random() * 1000000));    // 设定规格项目 ID（随机 6 位数）
        let _modal_data = $(MODAL_DATA_ID).val();

        // 规格数据转换成 JSON 对象
        _modal_data = JSON.parse(_modal_data);

        // 设定规格项目默认值
        let _option_list = _modal_data[_modal_id]['list'] || {}

        // 设定新建规格项目数据
        _option_list[_option_id] = {
            id: _option_id,
            order: _.size(_option_list) + 1
        };

        // 写入数据保存表单
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));

        // 生成规格列表和价格库存表格
        productSku.table(_modal_data);
    }

    /**
     * 更新产品规格名称
     *
     * @param object current
     */
    static optionUpdated = (current) => {

        let _modal_id = current.parents(MODAL_OPTION_ID).data('modal');
        let _option_id = current.parents(MODAL_OPTION_ID).data('option');
        let _modal_data = $(MODAL_DATA_ID).val();

        // 规格数据转换成 JSON 对象
        _modal_data = JSON.parse(_modal_data);

        // 设定当前规格 ID 数据的规格名称
        _modal_data[_modal_id]['list'][_option_id]['name'] = current.val();

        // 写入数据保存表单
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));

        // 生成规格列表和价格库存表格
        productSku.table();
    }

    /**
     * 删除产品规格项目
     *
     * @param object current
     */
    static optionDelete = (current) => {

        let _modal_id = current.parents(MODAL_OPTION_ID).data('modal');
        let _option_id = current.parents(MODAL_OPTION_ID).data('option');
        let _modal_data = $(MODAL_DATA_ID).val();
        let _price_data = $(PRICE_DATA_ID).val() || {};

        // 规格数据转换成 JSON 对象
        _modal_data = JSON.parse(_modal_data);

        // 判断库存价格数据，转换成 JSON 对象
        if (_price_data.length && 'object' != typeof _price_data) {
            _price_data = JSON.parse(_price_data)
        }

        // 删除当前规格项目 ID 的数据内容
        delete _modal_data[_modal_id]['list'][_option_id];

        // 遍历价格库存数据，删除包括当前规格项目 ID 的数据内容
        $.each(_price_data, function (key) {
            if (-1 !== key.indexOf(_option_id)) {
                delete _price_data[key];
            }
        });

        // 写入数据保存表单
        $(MODAL_DATA_ID).val(JSON.stringify(_modal_data));
        $(PRICE_DATA_ID).val(JSON.stringify(_price_data));

        // 生成规格列表和价格库存表格
        productSku.table(_modal_data);
    }

    /**
     * 更新产品价格库存
     *
     * @param object current
     */
    static updated = (current) => {
        let _sku = current.parents('tr').data('sku');   // 获取 SKU 名称
        let _type = current.data('type');               // 获取更新项目类型（保存的 key）
        let _price_data = $(PRICE_DATA_ID).val() || {};

        // 判断库存价格数据，转换成 JSON 对象
        if (_price_data.length && 'object' != typeof _price_data) {
            _price_data = JSON.parse(_price_data)
        }

        // 判断当前 SKU 的内容数据为空，使用默认 SKU 数据
        if ('undefined' == typeof _price_data[_sku]) {
            _price_data[_sku] = MODAL_DEFAULT_SKU
        }

        // 判断为 checkbox 表单类型
        if ('checkbox' == current.attr('type')) {

            // 判断表单选中设置 disabled 内容
            if (current.is(':checked')) {
                _price_data[_sku][_type] = 1
            } else {
                _price_data[_sku][_type] = 0
            }
        }
        // 其他类型表单设定为当前类型的表单值
        else {
            _price_data[_sku][_type] = current.val();
        }

        // 写入数据保存表单
        $(PRICE_DATA_ID).val(JSON.stringify(_price_data));
    }

    /**
     * 生成规格列表和价格库存表格
     *
     * @param object data
     */
    static table = (data) => {

        let _modal_data = data || $(MODAL_DATA_ID).val() || {};
        let _price_data = $(PRICE_DATA_ID).val() || {};
        let _modal_option = { color: '颜色', size: '尺码', modal: '规格' }

        // 判断规格数据，转换成 JSON 对象
        if (_modal_data.length && 'object' != typeof _modal_data) {
            _modal_data = JSON.parse(_modal_data)
        }

        // 判断库存价格数据，转换成 JSON 对象
        if (_price_data.length && 'object' != typeof _price_data) {
            _price_data = JSON.parse(_price_data)
        }

        // 显示规格列表内容
        $('#wpmall-modal-wrapper').html(webui.template(TMPL_MODAL, { option: _modal_option, modal: _modal_data }));

        // 判断规格列表内容为空时，价格库存 metabox 显示内容
        if (0 == _.size(_modal_data)) {
            $('#wpmall-price-wrapper').html('<div class="lead mt-2">请先添加规格类型。</div>');
            return
        }

        // 显示价格库存表格内容
        $('#wpmall-price-wrapper').html(webui.template(TMPL_PRICE, { option: _modal_option, modal: _modal_data, table: _price_data, default: MODAL_DEFAULT_SKU }));
    }

    /**
     * 生成价格库存内容数组函数
     */
    static createPrice = () => {

        // 初始化价格数组
        let _price_data = {}

        // 遍历价格表格
        $('tr[data-sku]').each(function () {

            let _this = $(this)
            let _sku_name = _this.data('sku')

            // 获取每一行价格库存信息
            _price_data[_sku_name] = {
                stock: _this.find('[data-type="stock"]').val(),
                buy: _this.find('[data-type="buy"]').val(),
                sale: _this.find('[data-type="sale"]').val(),
                vip: _this.find('[data-type="vip"]').val(),
                code: _this.find('[data-type="code"]').val()
            }

            // 判断禁用选中
            // if (_this.find('[data-type="disabled"]').is(':checked')) {
            //     _price_data[_sku_name]['disabled'] = 1
            // } else {
            //     _price_data[_sku_name]['disabled'] = 0
            // }
        })

        // 写入数据保存表单
        $(PRICE_DATA_ID).val(JSON.stringify(_price_data));
    }
}

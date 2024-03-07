/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import sku from './product-sku';

/**
 * 产品内容规格编辑操作模块
 *
 * @since 1.0.0
 */
export default class productPost {

    /**
     * 同步产品编码
     */
    static syncbarcode = () => {

        // 获取产品编码
        let _barcode = $('#wpmall-barcode').val()

        if (_barcode) {
            // 遍历价格库存表格
            $('tr[data-skuname]').each(function () {

                // 设定产品编码
                let _this = $(this)
                let _code_name = _barcode + '-' + _this.data('skuname')

                // 写入产品编码值
                _this.find('[data-type="code"]').val(_code_name)
            })
        }

        sku.createPrice()
    }

    /**
     * 同步商品价格
     */
    static syncprice = () => {
        // 获取销售价格和会员价格
        let _price_buy = $('#wpmall-buy').val()
        let _price_sale = $('#wpmall-sale').val()
        let _price_vip = $('#wpmall-vip').val()

        // 设定产品价格
        $('tr[data-sku]').find('[data-type="buy"]').val(_price_buy)
        $('tr[data-sku]').find('[data-type="sale"]').val(_price_sale)
        $('tr[data-sku]').find('[data-type="vip"]').val(_price_vip)

        // 生成价格内容数组
        sku.createPrice()
    }

    /**
     * 同步产品库存
     */
    static syncstock = () => {
        // 获取设定的库存
        let _stock = $('#wpmall-stock').val()
        $('tr[data-sku]').find('[data-type="stock"]').val(_stock)

        // 生成价格内容数组
        sku.createPrice()
    }

    /**
     * 一键生成衣服规格
     */
    static createssize = () => {
        let _size_type = $('#choosesize').val()
        let _size_eng = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL'];
        let _size_num = ['90#', '100#', '110#', '120#', '130#', '140#', '150#', '160#'];
        let _modal_data = {}
        let _modal_id = Math.ceil(Math.random() * 1000000);

        _modal_data[_modal_id] = {
            id: _modal_id,
            type: 'size',
            list: {},
            order: 0
        }

        for (let index = 0; index < 8; index++) {

            let _option_id = String(Math.ceil(Math.random() * 1000000));
            _modal_data[_modal_id].list[_option_id] = {
                id: _option_id,
                order: index
            }

            if ('eng' == _size_type) {
                _modal_data[_modal_id].list[_option_id].name = _size_eng[index]
            }

            if ('num' == _size_type) {
                _modal_data[_modal_id].list[_option_id].name = _size_num[index]
            }
        }

        // 写入数据保存表单
        $('#wpmall-modal-data').val(JSON.stringify(_modal_data));
        $('#wpmall-price-data').empty();

        // 生成规格列表和价格库存表格
        sku.table(_modal_data);
    }
}

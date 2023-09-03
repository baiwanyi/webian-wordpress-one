/**
 * ------------------------------------------------------------------------
 * 常量设定
 * ------------------------------------------------------------------------
 */
const TMPL_LOCATION_MODAL = 'wwpo-mall-customer-location-modal';
const TMPL_LOCATION_TABLE = 'wwpo-mall-customer-location-table';
const DISPLAY_LIST_ID = '#wwpo-mall-location-list';
const LOCATION_DATA_ID = '#wwpo-mall-customer-location';
const LOCATION_CURRENT_ID = '#wwpo-mall-current-location';

/**
 * ------------------------------------------------------------------------
 * 用户搜索请求
 * ------------------------------------------------------------------------
 */
webui.input('[data-action="wwpomallselectuser"]', (current) => {
    let _input_value = current.val();
    let _input_id = current.attr('id');
    let _datalist = $('#datalist' + _input_id);
    let _input_action = current.data('action')

    // 判断搜索关键字
    if (0 == _input_value.length) {
        return
    }

    // 判断 Body 样式，防止进行多次请求
    if ($('body').hasClass(_input_action)) {
        return
    }

    // 清空显示列表
    _datalist.empty();

    // 请求开始
    webui.ajax(null, {
        method: 'GET',
        login: 'wp',
        ajax: '/wp/v2/users',
        data: {
            roles: current.data('role'),
            search: _input_value
        },
        beforesend: () => {
            $('body').addClass(_input_action)
        },
        success: (result) => {
            $.each(result, function (i, item) {
                _datalist.append('<option data-user="' + item.id + '" value="' + item.slug + '">' + item.name + '</option>');
            });
        }
    })
});

/**
 * ------------------------------------------------------------------------
 * 用户搜索选择
 * ------------------------------------------------------------------------
 */
webui.change('[data-action="wwpomallselectuser"]', (current) => {
    let _input_value = current.val();
    let _input_action = current.data('action')
    let _user_id = $('option[value="' + _input_value + '"]').data('user')

    // 设定选择的用户 ID 及清除限制样式
    $('input[name="user_selected"]').val(_user_id)
    $('body').removeClass(_input_action)
});

/**
 * ------------------------------------------------------------------------
 * 显示客户地区选择侧边栏
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="wwpomallselectmodal"]', () => {
    webui.sidebar({
        title: '地址选择',
        backdrop: true,
        size: 'end w-75',
        content: webui.load(TMPL_LOCATION_MODAL)
    });

    wwpo_mall_search_location()
});

/**
 * ------------------------------------------------------------------------
 * 客户地区搜索操作
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="wwpomodalsearchlocation"]', () => {
    wwpo_mall_search_location()
});

/**
 * ------------------------------------------------------------------------
 * 客户地区搜索结果选择
 * ------------------------------------------------------------------------
 */
webui.click('[data-action="wwpomallselectlocation"]', (current) => {
    let index = current.val();
    let _location_data = $(LOCATION_DATA_ID).val() || {};

    // 转换成 JSON 对象
    if (_location_data.length && 'object' != typeof _location_data) {
        _location_data = JSON.parse(_location_data)
    }

    let _current_data = _location_data[index] || {};

    // 显示选择客户地区内容
    if (_current_data) {

        // 区域
        $('#user_location_area').val(_current_data.ad_info.province + ' / ' + _current_data.ad_info.city + ' / ' + _current_data.ad_info.district)

        // 详细地址
        $('#user_location_address').val(_current_data.address);

        // 写入 JSON
        $(LOCATION_CURRENT_ID).val(JSON.stringify(_current_data));
    }

    // 关闭侧边栏
    webui.main.sidebar.close('#webui-sidebar-main')
});

/**
 * ------------------------------------------------------------------------
 * 客户地区搜索函数
 * ------------------------------------------------------------------------
 */
var wwpo_mall_search_location = () => {

    // 设定客户地区和搜索关键字
    let _city_id = $('#wwpo-modal-location-region').val();
    let _search_key = $('#wwpo-modal-location-search').val();

    if (!_city_id || !_search_key) {
        return
    }

    // 地图 API 搜索请求
    $.ajax({
        url: 'https://apis.map.qq.com/ws/place/v1/search',
        dataType: 'jsonp',
        data: {
            key: WPMallSettings.mapapi,
            boundary: 'region(' + _city_id + ')',
            keyword: _search_key,
            output: 'jsonp'
        },
        beforesend: () => {
            $(DISPLAY_LIST_ID).html('<tr><td colspan="5" class="noitems text-center"><span class="webui-loading small"></span></td></tr>');
        },
        success: (result) => {

            if (0 != result.status) {
                $(DISPLAY_LIST_ID).find('.noitems').html(result.message).addClass('text-danger');
                return
            }

            if (0 == result.count) {
                $(DISPLAY_LIST_ID).find('.noitems').html('没有找到相关内容');
                return
            }

            webui.html(DISPLAY_LIST_ID, TMPL_LOCATION_TABLE, result.data);
            $(LOCATION_DATA_ID).val(JSON.stringify(result.data));
        }
    })
}

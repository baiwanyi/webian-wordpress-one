
webui.click('[data-action="wwpomallorderpayment"]', (current) => {
    let _action = current.val()

    if ('updated' == _action) {
        return
    }

    if ('sidebar' == _action) {
        let _title = current.text()
        webui.sidebar({
            title: _title + '付款信息',
            backdrop: true,
            button: {
                updated: { action: 'wwpomallorderpayment', title: '保存更改', css: 'btn btn-primary' }
            }
        })
    }
});

webui.click('[data-action="wwpomallorderstatus"]', (current) => {
    let _action = current.val()

    if ('updated' == _action) {
        return
    }

    if ('sidebar' == _action) {
        let _title = current.text()
        webui.sidebar({
            title: _title + '订单状态',
            backdrop: true,
            button: {
                updated: { action: 'wwpomallorderstatus', title: '保存更改', css: 'btn btn-primary' }
            }
        })
    }
});
webui.click('[data-action="wwpomallorderprint"]', (current) => {
    webui.sidebar({
        title: '打印预览',
        backdrop: true,
        size: 'top h-50',
        button: {
            printing: { action: 'wwpomallorderprint', title: '打印', css: 'btn btn-primary' },
            screenshot: { action: 'wwpomallorderprint', title: '保存图片', css: 'btn btn-outline-primary' }
        }
    })
});

webui.click('[data-action="checkcustomerphone"]', (current) => {
    webui.ajax(current, {
        ajaxurl: ajaxurl,
        form: null,
        data: {
            action: 'wwpoupdatepost',
            ajax: 'checkcustomerphone',
            pagenonce: webuiSettings.pagenonce,
            pagenow: webuiSettings.pagenow
        },
        beforesend: () => {
            $('input[name="user_customer[display]"]').val('')
            $('input[name="user_customer[contact]"]').val('')
            $('textarea[name="user_customer[address]"]').val('')
        }
    })
});

/**
 *
 */
webui.change('select[name="user_agent"]', (current) => {
    webui.wp.ajax('GET', 'getuseragentrate', {
        data: {
            user_agent: current.val()
        },
        success: totalPaymentPrice()
    })
})

var totalPaymentPrice = () => {
    // webui.toast('success', '成功', true)
}

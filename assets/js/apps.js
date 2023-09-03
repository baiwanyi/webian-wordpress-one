if ($('#markdown-toc')) {
    let _markdown = $('#markdown-toc')

    if (_markdown.length) {
        let _toc = _markdown.html()
        $('#site-markdown-toc').before('<h4>目录</h4>').html(_toc)
        _markdown.remove()
    }
}

webui.click('a[id^="markdown-toc"]', (current) => {
    let _href = current.attr('href')
    let _scrollTop = $(_href).offset().top - 90
    $('body,html').animate(
        {
            scrollTop: _scrollTop,
        },
        50
    )
    return false
})

if ($('#webui-qrcode')) {
    webui.qrcode('#webui-qrcode', 'WEBUI 前端框架')
}

if ($('#webui-qrcode64')) {
    webui.qrcode64('#webui-qrcode64', 'WEBUI 前端框架')
}

if ($('[role="loading"]')) {
    $('[role="loading"]').each(function () {
        let _this = $(this)
        _this.html(webui.loading(_this.data('align')))
    })
}

if ($('[role="nextpaged"]')) {
    $('[role="nextpaged"]').html(webui.nextpaged(2, 10, 'refresh'))
}

if ($('[role="paged"]')) {
    $('[role="paged"]').html(webui.paged(2, 10, 'refresh'))
}

webui.click('button[data-wb-action="alert"]', (current) => {
    webui.alert(current.val(), '这里是 ' + current.html())
})

webui.click('button[data-wb-action="message"]', (current) => {
    webui.message({
        user: 'WEBUI 前端框架',
        time: '2 分钟前',
        position: current.val(),
        content: current.html()
    });
})

webui.click('button[data-wb-action="toast"]', (current) => {
    webui.toast(current.val(), current.html(), true)
})

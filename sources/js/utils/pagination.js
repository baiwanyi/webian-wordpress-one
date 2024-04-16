/**
 * 页码类
 *
 * @since 1.0.0
 */
export default class pagination {
    /**
     * 页码分页函数
     *
     * @since 1.0.0
     * @param integer   paged       当前页码
     * @param integer   maxpaged    最大页码
     * @param string    action      加载内容 AJAX 动作
     */
    static paged = (paged, maxpaged, action) => {
        // 设定动作默认值
        paged = Number(paged)
        maxpaged = Number(maxpaged)
        action = action || ''

        if (1 >= maxpaged) {
            return
        }

        // 判断总页码数小于 5 页，最大显示页码数则为总页码数
        let max_show = 5 > maxpaged ? maxpaged : 5

        // 判断当前页面为 1，禁用上一页按钮
        let disabled_previous = 1 == paged ? ' disabled' : ''

        // 判断当前页面为最后一页，禁止下一页按钮
        let disabled_next = maxpaged == paged ? ' disabled' : ''

        // 链接模版
        let tmpl_link =
            '<li class="page-item{{data.css}}"><button class="page-link" data-action="' +
            action +
            '" data-paged="{{data.paged}}">{{{data.title}}}</button></li>'

        // 无链接模版
        let tmpl_nolink =
            '<li class="page-item{{data.css}}"><span class="page-link{{data.span}}">{{{data.title}}}</span></li>'

        // 分类开始
        let html = '<ul class="pagination">'

        // 返回上一页按钮
        html += wwpo.template(tmpl_link, {
            paged: paged - 1,
            css: disabled_previous,
            title: '&laquo;',
        })

        /** 判断当前页码大于最大显示数时，显示第一页按钮和省略号 */
        if (max_show < paged) {
            html += wwpo.template(tmpl_link, { paged: 1, title: 1 })
            html += wwpo.template(tmpl_nolink, {
                title: '&hellip;',
                span: ' text-muted',
                css: ' disabled',
            })
        }

        // 遍历最大页码数，显示页面列表
        _.times(max_show, function (i) {
            // 设定当前页面数
            // 当前页码数小于等于最大页码，则直接使用遍历的编号进行设置页码。（初始值为 0 ）
            // 大于最大页码数时，则使用当前页码数进行计算，因为最大显示页码数为 5 页，为保证当前页码前后都有相连页码可以点击，所以偏移两个数 -2 后再加上遍历的序号
            let current = max_show >= paged ? 1 + i : paged - 2 + i
            let data = { paged: current, title: current }

            // 判断为当前页码数，设定激活样式
            if (current == paged) {
                data['css'] = ' active'
            }

            // 判断当前页码数大于总页码数，则跳过
            if (current > maxpaged) {
                return true
            }

            // 显示页码
            html += wwpo.template(tmpl_link, data)
        })

        /** 最大页码数大于 5 页，并且当前页码数多 3 页时，显示省略号和最后一页按钮 */
        if (5 < maxpaged && 3 <= maxpaged - paged) {
            html += wwpo.template(tmpl_nolink, {
                title: '&hellip;',
                span: ' text-muted',
                css: ' disabled',
            })
            html += wwpo.template(tmpl_link, {
                paged: maxpaged,
                title: maxpaged,
            })
        }

        // 下一页按钮
        html += wwpo.template(tmpl_link, {
            paged: paged + 1,
            css: disabled_next,
            title: '&raquo;',
        })

        // 分页结束
        html += '</ul>'

        // 返回分页内容
        return html
    }

    /**
     * 下一页分页
     *
     * @since 1.0.0
     * @param integer   paged       当前页码
     * @param integer   maxpaged    最大页码
     * @param string    action      加载内容 AJAX 动作
     */
    static next = (paged, maxpaged, action) => {
        paged = Number(paged)
        maxpaged = Number(maxpaged)
        action = action || ''

        if (1 >= maxpaged) {
            return
        }

        // 按钮模版
        let button =
            '<button class="btn btn-outline-primary" type="button" data-action="{{data.action}}" data-paged="{{data.paged}}">{{data.title}}</button>'

        // 分页开始
        let html =
            '<div id="site-paged" class="btn-toolbar align-items-center my-3">'
        html += '<div class="btn-group">'

        /** 当前页码大于 1 显示上一页按钮 */
        if (1 < paged) {
            html += wwpo.template(button, {
                paged: paged - 1,
                title: '上一页',
                action: action,
            })
        }

        /** 当前页码小于总页数，显示下一页按钮 */
        if (maxpaged > paged) {
            html += wwpo.template(button, {
                paged: paged + 1,
                title: '下一页',
                action: action,
            })
        }

        html += '</div>'
        html += '<span class="text-mustd ms-2">共 ' + maxpaged + ' 页</span>'
        // 分页结束
        html += '</div>'

        // 显示按钮
        return html
    }
}

/**
 * 滚动监听
 *
 * @since 1.0.0
 */

/**
 * ------------------------------------------------------------------------
 * 引用文件
 * ------------------------------------------------------------------------
 */
import { marked } from 'marked'

/**
 * ------------------------------------------------------------------------
 * 暴露命名空间
 * ------------------------------------------------------------------------
 */
export default class markdown {

    /**
     * 数字金额转中文大写
     *
     * @since 1.0.0
     * @param integer money
     */
    static render = (layout) => {

        layout = layout || '#wwpo-layout'

        let _layout = jQuery(layout)
        let _markdown_id = _layout.data('wwpoMarkdown')
        let _markdown_content = jQuery(_markdown_id).html()

        if (_.isUndefined(_markdown_id) || 0 == _markdown_content.length) {
            return
        }

        // Override function
        let renderer = {
            heading(text, level) {
                return `<h${level} class="anchor" id="${wwpo.string.random(12)}">${text}</h${level}>`
            }
        }

        marked.use({ renderer }, {
            hooks: {
                postprocess(content) {
                    return `<div id="wwpo-markdown-main" class="wwpo__admin-content">${content}</div><aside id="wwpo-markdown-toc" class="wwpo__admin-toc">${markdown.headings(content)}</aside>`
                }
            }
        })

        _layout.html(marked.parse(_markdown_content))
    }

    static headings = (content) => {

        // 正则表达式匹配带有id属性的<h2>和<h3>标签及其内容
        const regex = /<(h2|h3) class="anchor" id="([^"]+)">(.*?)<\/(h2|h3)>/gi

        // 使用正则表达式的exec或test方法，或者String的match方法
        let match
        let matches = []
        while (null !== (match = regex.exec(content))) {
            // match[1] 是标签名 (h2 或 h3)
            // match[2] 是id属性值
            // match[3] 是标题文本内容
            matches.push({
                tag: match[1],
                id: match[2],
                content: match[3].trim() // 使用trim()去除可能的前后空白字符
            })
        }

        if (_.isEmpty(matches)) {
            return ''
        }

        return wwpo.template('<h4>页面导航</h4><ul><% _.each(data, function(item) { %><li class="{{item.tag}}"><a href="#{{item.id}}" rel="anchor">{{item.content}}</a></li><% }) %></ul>', matches)
    }
}

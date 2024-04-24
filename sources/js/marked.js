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
import prism from 'prismjs'
import wwpo from './wwpo'
import { marked } from 'marked'
// import fm from 'front-matter'

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
    static render = () => {

        let hash = window.location.hash || null
        let markdown_file_name = 'README.md'
        let markdown_file_base = wwpoSettings.markdown_current_tab
        let markdown_layout = jQuery('#wwpo-admin-docs')
        let markdown_base_url = wwpoSettings.markdown_base_url

        if (0 == markdown_layout.length) {
            return
        }

        if ('wwpo' != markdown_file_base) {
            markdown_base_url = markdown_base_url + 'modules' + '/' + markdown_file_base + '/'
        }

        // 创建一个自定义的渲染器
        const renderer = new marked.Renderer()

        renderer.heading = (text, level) => {

            if (1 == level) {
                return `<div class="h1">${text}</div>`
            }

            return `<h${level} class="anchor" id="${wwpo.string.random(12)}">${text}</h${level}>`
        }

        renderer.link = (href, title, text) => {
            if (href.includes('http')) {
                return `<a href="${href}" target="_blank">${text}</a>`;
            }

            return `<a href="${href}" rel="markdown">${text}</a>`;
        }

        renderer.image = (href, title, text) => {
            return `<figure class="figure"><img src="${markdown_base_url}${href.replace(/^.\//, 'docs/')}" class="thumb"><figcaption class="caption">${text}</figcaption></figure>`;
        }

        renderer.table = (header, body) => {
            // 在这里，你可以根据需要对 header 和 body 进行自定义处理
            // header 是一个包含表头单元格的数组
            // body 是一个二维数组，其中每个内部数组代表表格的一行

            // 例如，为表格添加自定义样式或类名
            return `<table class="wwpo__admin-table"><thead><tr>${header}</tr></thead><tbody>${body}</tbody></table>`;
        };

        marked.use({ renderer })

        if (hash) {
            markdown_file_name = hash.replace(/^#!\//, '')
        }

        jQuery.ajax({
            url: markdown_base_url + markdown_file_name,
            type: 'GET',
            dataType: 'text',
            beforeSend: () => {
                markdown_layout.html('<span class="wwpo-loading small"></span>')
            },
            success: (result) => {
                if (_.isEmpty(result)) {
                    markdown_layout.html('<div class="notice notice-error"><p>没有找到相关内容</p></div>')
                    return
                }

                let data = markdown.frontmatter(result)
                let title = data.attributes.title || '';
                let description = data.attributes.description || '';
                let updated = data.attributes.updated || '';
                let toc = data.attributes.toc || false
                let content = marked.parse(data.body)
                let html = ''

                if (title) {
                    html += `<div class="h1">${title}</div>`
                }

                if (description) {
                    html += `<p>${description}</p>`
                }

                if (updated) {
                    html += `<p><strong>更新日期：</strong>${updated}</p>`
                }

                html += '<div class="wwpo__admin-body">'

                if (toc) {
                    html += '<aside id="wwpo-admin-toc" class="wwpo__admin-toc"></aside>';
                }

                if ('undefined' != typeof wwpoSettings.markdown_sidebar) {
                    html += markdown.sidebar()
                }

                html += `<div id="wwpo-admin-content" class="wwpo__admin-content">${content}</div>`

                html += '</div>'

                markdown_layout.html(html)

                jQuery('#wwpo-admin-toc').html(markdown.headings(content))

                prism.highlightAll()
            }
        })
    }

    static frontmatter = (content) => {

        let pattern = '^(' +
            '\\ufeff?' +
            '(= yaml =|---)' +
            '$([\\s\\S]*?)' +
            '^(?:\\2|\\.\\.\\.)\\s*' +
            '$' +
            '(?:\\n)?)'

        let regex = new RegExp(pattern, 'm')
        let match = regex.exec(content)
        let attributes = {}
        var first = content.split(/(\r?\n)/)

        if (first[0] && /= yaml =|---/.test(first[0])) {

            let fm = match[match.length - 1].replace(/^\s+|\s+$/g, '')
            let body = content.replace(match[0], '')
            let lines = fm.split(/\r?\n/)

            lines.forEach(line => {
                const [title, content] = line.split(': '); // 分割键和值
                attributes[title.trim()] = content.trim()
            })

            return {
                attributes: attributes,
                body: body
            }
        }

        return {
            attributes: attributes,
            body: content
        }
    }

    static sidebar = () => {

        let sidebar = wwpo.template('<% _.each(data, function(item, url) { %><a href="{{url}}" class="item">{{item.title}}</a><% if(item.menu){ %><ul class="submenu"><% _.each(item.menu, function(title, sub_url){ %><li><a href="{{sub_url}}"class="menu">{{title}}</a></li><% }) %></ul><% } %><% }) %>', JSON.parse(wwpoSettings.markdown_sidebar))

        return `<nav id="wwpo-admin-sidebar" class="wwpo__admin-sidebar">${sidebar}</nav>`
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

        if (_.isEmpty(matches) || 2 > _.size(matches)) {
            return ''
        }

        return wwpo.template('<h4>页面导航</h4><ul><% _.each(data, function(item) { %><li class="{{item.tag}}"><a href="#{{item.id}}" rel="anchor">{{item.content}}</a></li><% }) %></ul>', matches)
    }
}

/**
 * ------------------------------------------------------------------------
 * 渲染 Markdown 格式内容
 * ------------------------------------------------------------------------
 */
jQuery(() => {
    markdown.render()

    window.addEventListener('hashchange', () => {
        markdown.render()
    })
})

/**
 * ------------------------------------------------------------------------
 * 渲染 Markdown 格式内容
 * ------------------------------------------------------------------------
 */
wwpo.click('a[rel="markdown"]', (current) => {
    let hash = current.attr('href').replace(/^\.\//, '')
    window.location.hash = '!/' + hash
    window.scrollTo(0, 0)
})

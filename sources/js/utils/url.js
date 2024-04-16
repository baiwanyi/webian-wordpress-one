/**
 * URL 相关函数
 *
 * @since 1.0.0
 * - parm       获取 URL 参数值
 * - replace    修改指定 URL 参数值
 */
export default class url {
    /*
     * 获取指定 URL 参数值数组
     *
     * @since 1.0.0
     * @var string url 需要获取的 URL 链接
     */
    static parms = (url) => {
        url = url || window.location.href
        var vars = {},
            hash
        var index = url.indexOf('?') + 1

        if (0 == index) {
            return vars
        }

        var hashes = url.slice(index).split('&')
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=')
            vars[hash[0]] = hash[1]
        }
        return vars
    }

    /**
     * 获取 URL 参数值
     *
     * @since 1.0.0
     * @param string name 需要获取的参数名称
     */
    static parm(name) {
        let _parms = url.parms()
        return _parms[name] || ''
    }

    /*
     * 修改指定 URL 参数值
     *
     * @since 1.0.0
     * @var string  param_name  需要替换的参数名称
     * @var string  param_val   替换后的参数的值
     * @var string  url         需要修改的 URL 链接
     */
    static replace(param_name, param_val, url) {
        url = url || window.location.href
        let pattern = param_name + '=([^&]*)'
        let replaceText = param_name + '=' + param_val
        if (url.match(pattern)) {
            let tmp = '/(' + param_name + '=)([^&]*)/gi'
            tmp = url.replace(eval(tmp), replaceText)
            return tmp
        } else {
            if (url.match('[?]')) {
                return url + '&' + replaceText
            } else {
                return url + '?' + replaceText
            }
        }
    }
}

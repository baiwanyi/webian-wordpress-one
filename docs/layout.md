# 页面

## 常量
| 名称      | 简介           | 示例                   |
| --------- | -------------- | ---------------------- |
| NOW       | 定义当前时间戳 | 1710345600             |
| NOW_TIME  | 格式化当前时间 | 2024年3月14日 00:00:00 |
| WWPO_ROLE | 默认权限       | activate_plugins       |

## 颜色
通过一些颜色实用程序类通过颜色传达意义。
| 颜色                                                             | HEX CODE | 应用区域 |
| ---------------------------------------------------------------- | -------- | -------- |
| <div style="height:24px; width:24px; background:#72aee6;"></div> | #72aee6  | -        |
| <div style="height:24px; width:24px; background:#2271b1;"></div> | #2271b1  | 主按钮   |
| <div style="height:24px; width:24px; background:#135e96;"></div> | #135e96  | -        |
| <div style="height:24px; width:24px; background:#043959;"></div> | #043959  | -        |
| <div style="height:24px; width:24px; background:#1d2327;"></div> | #1d2327  | -        |
| <div style="height:24px; width:24px; background:#2c3338;"></div> | #2c3338  | -        |
| <div style="height:24px; width:24px; background:#a7aaad;"></div> | #a7aaad  | -        |
| <div style="height:24px; width:24px; background:#c3c4c7;"></div> | #c3c4c7  | -        |
| <div style="height:24px; width:24px; background:#f0f0f1;"></div> | #f0f0f1  | 背景颜色 |
| <div style="height:24px; width:24px; background:#646970;"></div> | #646970  | -        |
| <div style="height:24px; width:24px; background:#d63638;"></div> | #d63638  | -        |
| <div style="height:24px; width:24px; background:#00a32a;"></div> | #00a32a  | -        |
| <div style="height:24px; width:24px; background:#dba617;"></div> | #dba617  | -        |

## 组件
### 按钮
<button type="button" class="button">默认按钮</button>
<button type="button" class="button button-primary">保存更改</button>
<button type="button" class="button button-link-delete">删除</button>
<button type="button" class="button button-link">链接格式</button>

```html
<button type="button" class="button">默认按钮</button>
<button type="button" class="button button-primary">保存更改</button>
<button type="button" class="button button-link-delete">删除</button>
<button type="button" class="button button-link">链接格式</button>
```

#### 按钮的尺寸
<button type="button" class="button button-large">大按钮</button>
<button type="button" class="button button-small">小按钮</button>

```html
<button type="button" class="button button-large">大按钮</button>
<button type="button" class="button button-small">小按钮</button>
```

#### 按钮的状态
<button type="button" class="button installing">动态加载</button>
<button type="button" class="button button-primary updated-message">成功状态</button>
<button type="button" class="button button-disabled">禁用状态</button>

```html
<button type="button" class="button installing">动态加载</button>
<button type="button" class="button button-primary updated-message">成功状态</button>
<button type="button" class="button button-disabled">禁用状态</button>
```

### 消息框
<aside class="notice-success notice"><p style="margin:0.5em 0">成功消息框</p></aside>
<aside class="notice-warning notice"><p style="margin:0.5em 0">警告消息框</p></aside>
<aside class="notice-error notice"><p style="margin:0.5em 0">错误消息框</p></aside>
<aside class="notice-info notice"><p style="margin:0.5em 0">信息消息框</p></aside>
<aside class="notice"><p style="margin:0.5em 0">默认消息框</p></aside>

```html
<div class="notice notice-success"><p>成功消息框</p></div>
<div class="notice notice-warning"><p>警告消息框</p></div>
<div class="notice notice-error"><p>错误消息框</p></div>
<div class="notice notice-info"><p>信息消息框</p></div>
<div class="notice"><p>默认消息框</p></div>
```

#### 带关闭按钮
<aside class="notice is-dismissible"><p style="margin:0.5em 0">带关闭按钮消息框</p></aside>

```html
<div class="notice is-dismissible"><p>带关闭按钮消息框</p></div>
```

### 内容框
<div id="poststuff" class="metabox-holder">
    <div class="postbox">
        <div class="postbox-header">
            <h2 style="border: none">标题</h2>
        </div>
        <div class="inside">内容</div>
    </div>
</div>


```html
<div class="postbox">
    <div class="postbox-header">
        <h2>标题</h2>
    </div>
    <div class="inside">内容</div>
</div
```

### 卡片
<p>
<div class="card">
    <h3 class="title">卡片标题</h3>
    <p>卡片内容</p>
</div>
</p>

```html
<div class="card">
    <h3 class="title">卡片标题</h3>
    <p>卡片内容</p>
</div>
```

### 导航
#### 选项卡
<nav class="nav-tab-wrapper mb-3">
    <a class="nav-tab nav-tab-active" href="#">选项卡一</a>
    <a class="nav-tab" href="#">选项卡二</a>
</nav>

#### 横向导航
<div class="wp-filter">
    <div class="filter-links">
        <li class="plugin-install-featured"><a href="https://wp.webian.dev/wp-admin/plugin-install.php?tab=featured"
               class="current" aria-current="page">特色</a> </li>
        <li class="plugin-install-popular"><a
               href="https://wp.webian.dev/wp-admin/plugin-install.php?tab=popular">热门</a> </li>
        <li class="plugin-install-recommended"><a
               href="https://wp.webian.dev/wp-admin/plugin-install.php?tab=recommended">推荐</a> </li>
        <li class="plugin-install-favorites"><a
               href="https://wp.webian.dev/wp-admin/plugin-install.php?tab=favorites">收藏</a></li>
    </div>
    <div class="search-form search-plugins" method="get">
        <input type="hidden" name="tab" value="search">
        <label class="screen-reader-text" for="typeselector">
            搜索插件： </label>
        <select name="type" id="typeselector">
            <option value="term" selected="selected">关键字</option>
            <option value="author">作者</option>
            <option value="tag">标签</option>
        </select>
        <label class="screen-reader-text" for="search-plugins">
            搜索插件 </label>
        <input type="search" name="s" id="search-plugins" value="" class="wp-filter-search" placeholder="搜索插件…"
               aria-describedby="live-search-desc">
        <input type="submit" id="search-submit" class="button hide-if-js" value="搜索插件">
    </div>
</div>


## 目录结构
```
模块目录
├─assets        静态文件目录
│   ├─css       CSS目录
│   ├─images    图片目录
│   └─js        JS目录
│
├─docs          文档目录
├─includes      插件文件目录
│   │
│   ├─class-db-sqlite.php   SQLite数据库类
│   ├─class-wwpo-admin.php  后台管理页面方法类
│
├─languages     语言文件目录
├─modules       模块文件目录
│   ├─index.php          入口文件
│   └─.htaccess          用于apache的重写
│
├─wwpo.php      插件加载文件
```

## 模块描述
 - 微信小程序，服务端接口支持
 - 微信认证服务号，服务端接口支持
 - 微信支付（账单、卡券、红包、退款、转账、App支付、JSAPI支付、Web支付、扫码支付等）
 - 支付宝支付（账单、转账、App支付、刷卡支付、扫码支付、Web支付、Wap支付等）
 - 头条支付
 - 云闪付支付

### 模块 - 插件常量
| 名称      | 简介           | 示例                   |
| --------- | -------------- | ---------------------- |
| NOW       | 定义当前时间戳 | 1710345600             |
| NOW_TIME  | 格式化当前时间 | 2024年3月14日 00:00:00 |
| WWPO_ROLE | 默认权限       | activate_plugins       |

## 模块描述
 - 微信小程序，服务端接口支持
 - 微信认证服务号，服务端接口支持
 - 微信支付（账单、卡券、红包、退款、转账、App支付、JSAPI支付、Web支付、扫码支付等）
 - 支付宝支付（账单、转账、App支付、刷卡支付、扫码支付、Web支付、Wap支付等）
 - 头条支付
 - 云闪付支付

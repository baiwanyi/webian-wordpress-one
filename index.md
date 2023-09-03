---
layout: page
---
<div class="container mt-5">
    <div class="row">
        <div class="col-4">
            <h3>配置简单</h3>
            <ul class="text-black-50 mt-3">
                <li>约定优于配置</li>
                <li>一个配置文件 <code>pagic.config.ts</code></li>
                <li>符合直觉的设计</li>
            </ul>
        </div>
        <div class="col-4">
            <h3>配置简单</h3>
            <ul class="text-black-50 mt-3">
                <li>约定优于配置</li>
                <li>一个配置文件 <code>pagic.config.ts</code></li>
                <li>符合直觉的设计</li>
            </ul>
        </div>
        <div class="col-4">
            <h3>配置简单</h3>
            <ul class="text-black-50 mt-3">
                <li>约定优于配置</li>
                <li>一个配置文件 <code>pagic.config.ts</code></li>
                <li>符合直觉的设计</li>
            </ul>
        </div>
    </div>

    <h2 class="mt-5">只需几行命令，快来体验吧</h2>
    <pre class="bg-light mt-3">
        <code>
        # 安装 pagic
        deno install --unstable --allow-read --allow-write --allow-net --allow-run --name=pagic https://deno.land/x/pagic/mod.ts

        # 创建 pagic.config.ts 和 README.md
        mkdir site &amp;&amp; cd site &amp;&amp; echo "export default {};" &gt; pagic.config.ts &amp;&amp; echo "# Hello world" &gt; README.md

        # 运行 pagic
        pagic build --watch --serve
    </code>
</pre>
</div>

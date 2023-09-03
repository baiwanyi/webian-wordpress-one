<?php

/**
 * 按钮显示类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Button
{
    /**
     * 按钮属性数组
     *
     * @since 1.0.0
     * @var array
     */
    public $attributes;

    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var WWPO_Button $instance 类实例参数
     */
    static protected $instance;

    /**
     * 输出提交按钮
     *
     * @since 1.0.0
     * @param array|string $data
     */
    static function wp($data)
    {
        if (empty(self::$instance) && !self::$instance instanceof WWPO_Button) {
            self::$instance = new WWPO_Button();
        }

        $defaults = [
            'css' => 'primary large'
        ];

        if (is_array($data)) {
            $defaults = wp_parse_args($data, $defaults);
        } else {
            $defaults['value'] = $data;
        }

        return self::$instance->_button_display($defaults);
    }

    /**
     * 输出提交按钮
     *
     * @since 1.0.0
     * @param array|string $data
     */
    static function submit($data)
    {
        if (empty(self::$instance) && !self::$instance instanceof WWPO_Button) {
            self::$instance = new WWPO_Button();
        }

        $defaults = [
            'type'  => 'submit',
            'css'   => 'primary large'
        ];

        if (is_array($data)) {
            $defaults = wp_parse_args($data, $defaults);
        } else {
            $defaults['value'] = $data;
        }

        return self::$instance->_button_display($defaults);
    }

    /**
     * AJAX 操作按钮设定函数
     *
     * @since 1.0.0
     * @param array $button_data
     * {
     *  按钮参数
     *  @var string     type        按钮类型：button普通按钮，submit提交按钮，默认：button
     *  @var string     text        按钮标签
     *  @var string     value       按钮值，AJAX 执行参数名
     *  @var string     css         按钮 css 样式
     *  @var array      dataset     按钮 data-* 属性，索引为属性名，数组值为属性值
     *  @var boolean    disabled    是否禁用按钮，默认：false
     * }
     */
    private function _button_display($button_data)
    {
        $defaults = [
            'type'      => 'button',
            'text'      => __('Save Changes'),
            'value'     => null,
            'css'       => null,
            'dataset'   => null,
            'disabled'  => false
        ];

        $button_data    = wp_parse_args($button_data, $defaults);
        $button_css     = $this->_button_css($button_data['css']);

        // 设定按钮的属性值
        $this->attributes = [];

        /** 显示 button 按钮 */
        if ('button' == $button_data['type']) {

            // 添加默认后台 AJAX 操作动作名
            if (empty($button_data['dataset']['action']) && is_admin()) {
                $button_data['dataset']['action'] = 'wpajax';
            }

            $this->attributes[] = 'type="button"';
        } else {
            $this->attributes[] = 'type="submit" name="submit"';
        }

        return sprintf('<button %s>%s</button>', $this->_button_attributes($button_css, $button_data), $button_data['text']);
    }

    /**
     * 设定按钮属性函数
     *
     * @since 1.0.0
     * @param array $dataset data-* 属性内容数组
     */
    private function _button_attributes($button_css, $button_data)
    {
        if (isset($button_css)) {
            $this->attributes[] = sprintf('class="%s"', esc_attr($button_css));
        }

        if (isset($button_data['name'])) {
            $this->attributes[] = sprintf('name="%s"', esc_attr($button_data['name']));
        }

        if (isset($button_data['value'])) {
            $this->attributes[] = sprintf('value="%s"', esc_attr($button_data['value']));
        }

        if (isset($button_data['disabled'])) {
            $this->attributes[] = disabled($button_data['disabled'], true, false);
        }

        if (isset($button_data['dataset'])) {
            foreach ($button_data['dataset'] as $dataset_key => $dataset_value) {
                $this->attributes[] = sprintf('data-%s="%s"', esc_attr($dataset_key), esc_attr($dataset_value));
            }
        }

        if (empty($this->attributes)) {
            return;
        }

        return implode(' ', $this->attributes);
    }

    /**
     * 设定按钮样式函数
     *
     * @since 1.0.0
     * @param array $button_css 按钮样式数组
     */
    private function _button_css($button_css)
    {
        if (empty($button_css)) {
            return;
        }

        // 设定按钮 WP 样式和样式默认 CSS 类
        $button_shorthand   = ['primary', 'small', 'large', 'link-delete'];
        $classes            = ['button'];
        $button_count       = 0;

        // 判断按钮 CSS 样式格式，字符串格式用空格打散为数组
        if (!is_array($button_css)) {
            $button_css = explode(' ', $button_css);
        }

        /**
         * 遍历传入 CSS 样式数组内容
         *
         * @property string $css 样式名称
         */
        foreach ($button_css as $css) {
            if ('secondary' === $css || 'button-secondary' === $css) {
                continue;
            }

            // 判断传入的 CSS 样式是 WP 样式，则添加 button 前缀
            if (in_array($css, $button_shorthand, true)) {
                $classes[] = 'button-' . $css;
                $button_count++;
            } else {
                $classes[] = $css;
            }
        }

        /** 判断没有 button 前缀的样式，则删除 button 样式*/
        if (empty($button_count)) {
            unset($classes[0]);
        }

        // 删除空项和重复项，生成一个 CSS 样式字符串。
        return implode(' ', array_unique(array_filter($classes)));
    }
}

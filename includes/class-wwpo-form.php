<?php

/**
 * 表单显示类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Form
{
    /**
     * 表单属性数组
     *
     * @since 1.0.0
     * @var array
     */
    public $attributes;

    /**
     * 表单内容
     *
     * @since 1.0.0
     * @var string
     */
    public $input_field;

    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var WWPO_Form $instance 类实例参数
     */
    static protected $instance;

    /**
     * 表格表单显示函数
     *
     * @since 1.0.0
     */
    static function table($data, $content = '')
    {
        if (empty($data['formdata'])) {
            return;
        }

        if (empty(self::$instance) && !self::$instance instanceof WWPO_Form) {
            self::$instance = new WWPO_Form();
        }

        if (isset($data['title'])) {
            $content .= sprintf('<h2 class="anchor" id="%1$s">%1$s</h2>', $data['title']);
        }

        if (isset($data['hidden'])) {
            $data['hidden']['_wwpourl'] = remove_query_arg(['select', 's', 'message']);
            $content .= self::hidden($data['hidden']);
        }

        // 初始化表格内容，防止循环中重复显示上一个表格的内容。
        $table_body     = '';
        $table_class    = $data['css'] ?? 'form-table';

        /**
         * 遍历表格表单内容
         *
         * @property string $form_key   表单 ID
         * @property array  $form_data
         * {
         * 表单内容数组
         *  @var array   field       单表单内容数组
         *  @var array   fields      多表单内容数组
         *  @var mixed   content     直接输出内容
         * }
         */
        foreach ($data['formdata'] as $form_key => $form_data) {

            // 初始化表单内容，防止循环中重复显示上一个表格的内容。
            $field_td = '';

            // 设定当前表单行标题
            // 删除表单标题数组内容，防止在 field 函数中重复输出表单标题
            $field_th = esc_html($form_data['title']);
            unset($form_data['title']);

            $field_td   .= self::$instance->_display_content($form_key, $form_data);
            $table_body .= sprintf('<tr><th>%1$s</th><td>%2$s</td></tr>', $field_th, $field_td);
        }

        $content .= sprintf('<table class="wwpo__form-table %s"><tbody>%s</tbody></table>', esc_attr($table_class), $table_body);

        /** 设定表单按钮 */
        if (isset($data['button'])) {
            return sprintf(
                '<form id="wwpo__admin-form" method="POST" autocomplete="off">%1$s<div class="submit">%2$s</div></form>',
                $content,
                // WWPO_Button::submit($data['button'])
            );
        }

        return $content;
    }

    /**
     * 列表表单显示函数
     *
     * @since 1.0.0
     */
    static function list($data, $content = '')
    {
        if (empty(self::$instance) && !self::$instance instanceof WWPO_Form) {
            self::$instance = new WWPO_Form();
        }

        if (isset($data['title'])) {
            $content .= sprintf('<h2>%1$s</h2>', $data['title']);
        }

        if (isset($data['hidden'])) {
            $data['hidden']['_wwpourl'] = remove_query_arg(['select', 's', 'message']);
            $content .= self::hidden($data['hidden']);
        }

        $form_class = $data['class'] ?? 'form-wrap';

        $content .= sprintf('<div class="%s">', esc_attr($form_class));

        /**
         * 显示列表表单内容
         *
         * @property string $field_key   表单 ID
         * @property mixed  $form_data  单表单内容
         */
        if (isset($data['formdata'])) {
            foreach ($data['formdata'] as $form_key => $form_data) {
                $content .= self::$instance->_display_content($form_key, $form_data);
            }
        }

        $content .= '</div>';

        /** 设定表单按钮 */
        if (isset($data['button'])) {
            return sprintf(
                '<form id="wwpo__admin-form" method="POST" autocomplete="off">%1$s<div class="submit">%2$s</div></form>',
                $content,
                // WWPO_Button::submit($data['button'])
            );
        }

        return $content;
    }

    /**
     * 获取表单函数
     *
     * @since 1.0.0
     * @param string    $input_id   表单 ID 名称
     * @param array     $data       表单内容数组
     */
    static function field($input_id, $data)
    {
        if (empty($data['field'])) {
            return;
        }

        if (empty(self::$instance) && !self::$instance instanceof WWPO_Form) {
            self::$instance = new WWPO_Form();
        }

        /** 设定 checkbox radio 表单 CSS 样式 */
        if (in_array($data['field']['type'], ['checkbox', 'radio'])) {
            $input_css = $data['field']['css'] ?? 'wwpo__checkbox-input';
        }
        /** 设定 select 表单 CSS 样式 */
        elseif ('select' == $data['field']['type']) {
            $input_css = $data['field']['css'] ?? 'wwpo__menu-list';
        }
        /** 设定 textarea 表单 CSS 样式 */
        elseif ('textarea' == $data['field']['type']) {
            $input_css = $data['field']['css'] ?? 'large-text code w-100';
        }
        /** 设定 input 表单 CSS 样式 */
        else {
            $input_css = $data['field']['css'] ?? 'regular-text';
        }

        if (isset($data['before']) || isset($data['after'])) {
            $input_css = ('select' == $data['field']['type']) ? 'form-select' : 'form-control';
        }

        $input_field = self::$instance->_display_field($input_id, $input_css, $data);

        return sprintf(
            '<div class="wwpo__admin-field %1$s">%2$s</div>',
            esc_attr($data['class'] ?? ''),
            $input_field
        );
    }

    /**
     * 隐藏表单
     *
     * @since 1.0.0
     * @param array     $data       表单内容数组
     * @param string    $hidden     初始化隐藏表单
     */
    static function hidden($data, $hidden = '')
    {
        if (empty($data)) {
            return $hidden;
        }

        /**
         * 显示隐藏表单内容
         *
         * @property string $hidden_key     表单 name 名
         * @property string $hidden_value   表单 name 值
         */
        foreach ($data as $hidden_key => $hidden_value) {
            $hidden .= sprintf(
                '<input type="hidden" name="%1$s" value="%2$s">',
                esc_attr($hidden_key),
                esc_attr($hidden_value)
            );
        }

        return $hidden;
    }

    /**
     * 多行文本输入表单函数
     *
     * @since 1.0.0
     * @param string    $input_id   表单 ID
     * @param string    $input_css  表单样式
     * @param array     $input_data
     * {
     *  表单参数
     *  @var integer    rows    表单行 rows 属性，默认：5
     *  @var string     value   表单值
     * }
     */
    public function textarea($input_id, $input_css, $input_data)
    {
        $this->attributes[] = sprintf('rows="%s"', esc_attr($input_data['rows']));
        $this->input_field  = sprintf('<textarea %1$s>%2$s</textarea>', $this->_attributes($input_id, $input_css, $input_data), $input_data['value']);
    }

    /**
     * INPUT 表单函数
     *
     * @since 1.0.0
     * @param string    $input_id   表单 ID
     * @param string    $input_css  表单样式
     * @param array     $input_data
     * {
     *  表单参数
     *  @var string type    表单类型，默认：text
     *  - checkbox          将输入限制为true / false复选框。
     *  - color             将输入限制为颜色。
     *  - date              将输入限制为日期。
     *  - datetime          将输入限制为具有时区的全球日期和时间。
     *  - datetime-local    将输入限制为不带时区的全局日期和时间。
     *  - email             将输入限制为格式正确的电子邮件地址。
     *  - month             将输入限制为年和月。
     *  - number            将输入限制为整数或浮点数。
     *  - radiobutton       将输入限制为固定的选项集。
     *  - range             将输入限制为指定范围。
     *  - tel               将输入限制为格式正确的电话号码。
     *  - time              将输入限制为一天中的时间。
     *  - week              将输入限制为年和周。
     *  - url               将输入限制为完全限定的URL。
     *  @var string value   表单值
     * }
     */
    public function input($input_id, $input_css, $input_data)
    {
        $this->attributes[] = sprintf('type="%s"', esc_attr($input_data['type']));
        $this->attributes[] = sprintf('value="%s"', $input_data['value']);
        $this->input_field  = sprintf('<input %s>', $this->_attributes($input_id, $input_css, $input_data));
    }

    /**
     * 下拉表单函数
     *
     * @since 1.0.0
     * @param string    $input_id   表单 ID
     * @param string    $input_css  表单样式
     * @param array     $input_data
     * {
     *  表单参数
     *  @var array option           选项内容数组，key 为选项值，value 为选项标题
     *  @var string show_option_value   默认选项值
     *  @var string show_option_all   默认选项名
     *  @var string selected        已选择的选项值
     * }
     */
    public function select($input_id, $input_css, $input_data)
    {
        $options = '';

        // 设定选项默认值
        if ($input_data['show_option_all']) {
            $options .= sprintf(
                '<option value="%1$s">%2$s</option>',
                esc_attr($input_data['show_option_value']),
                esc_html($input_data['show_option_all'])
            );
        }

        /**
         * 遍历选项值内容数组
         *
         * @property string $option_value   选项值
         * @property string $option_title   选项名
         */
        if (isset($input_data['option'])) {
            foreach ($input_data['option'] as $option_value => $option_title) {
                $options .= sprintf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    esc_attr($option_value),
                    selected($option_value, $input_data['selected'], false),
                    esc_html($option_title)
                );
            }
        }

        /**
         * 遍历选项值内容数组
         *
         * @property string $option_value   选项值
         * @property string $option_title   选项名
         */
        if (isset($input_data['group'])) {
            foreach ($input_data['group'] as $group_val) {

                if (empty($group_val['option'])) {
                    continue;
                }

                $options .= sprintf(
                    '<optgroup label="%s">',
                    $group_val['title']
                );

                foreach ($group_val['option'] as $option_value => $option_title) {
                    $options .= sprintf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        esc_attr($option_value),
                        selected($option_value, $input_data['selected'], false),
                        esc_html($option_title)
                    );
                }
            }
        }

        // 输出表单内容
        $this->input_field = sprintf(
            '<select %1$s>%2$s</select>',
            $this->_attributes($input_id, $input_css, $input_data),
            $options
        );
    }


    /**
     * Undocumented function
     *
     * @since 1.0.0
     * @param [type] $input_id
     * @param [type] $input_css
     * @param [type] $input_data
     */
    public function datalist($input_id, $input_css, $input_data)
    {
        $this->attributes[] = 'type="text"';
        $this->attributes[] = sprintf('list="datalist%s"', esc_attr($input_id));
        $this->attributes[] = sprintf('value="%s"', $input_data['value']);


        $options = sprintf('<datalist id="datalist%s">', esc_attr($input_id));

        /**
         * 遍历选项值内容数组
         *
         * @property string $option_value   选项值
         * @property string $option_title   选项名
         */
        if ($input_data['option']) {
            foreach ($input_data['option'] as $option_value => $option_title) {
                $options .= sprintf(
                    '<option value="%s">%s</option>',
                    esc_attr($option_value),
                    esc_html($option_title)
                );
            }
        }

        $options .= '</datalist>';

        $this->input_field  = sprintf('<input %s>%s', $this->_attributes($input_id, $input_css, $input_data), $options);
    }

    /**
     * 表单内容显示函数
     *
     * @since 1.0.0
     * @param string    $form_type  表单类型：table/list
     * @param array     $data
     * {
     *  表单内容数组
     *  @var array  formdata    表单内容
     *  @var string title       表格标题
     *  @var string form_id     表单 ID
     *  @var string css         表单样式
     *  @var array  button      提交按钮，为空不显示，使用方法：@see WWPO_Form::submit
     *  @var array  hidden      隐藏表单内容数组，为空不显示，使用方法：@see WWPO_Form::hidden
     * }
     * @param boolean   $form_wrap  包含 form 标签
     */
    private function _display_content($form_key, $form_data, $content = '')
    {
        if (isset($form_data['button'])) {
            // $content .= WWPO_Button::submit($form_data['button']);
        }

        /** 判断直接显示内容 */
        if (isset($form_data['content'])) {
            $content .= $form_data['content'];
        }

        /** 判断使用单个表单内容 */
        if (isset($form_data['field'])) {
            $content .= self::field($form_key, $form_data);
        }

        /** 判断使用多个表单内容 */
        if (isset($form_data['fields'])) {

            $content .= '<div class="wwpo__admin-group">';

            if (isset($form_data['title'])) {
                $content .= sprintf('<label class="wwpo__input-label">%s</label>', $form_data['title']);
            }

            foreach ($form_data['fields'] as $field_key => $field_data) {

                if (isset($field_data['button'])) {
                    // $content .= WWPO_Button::submit($field_data['button']);
                }

                /** 判断直接显示内容 */
                if (isset($field_data['content'])) {
                    $content .= $field_data['content'];
                }

                /** 判断使用单个表单内容 */
                if (isset($field_data['field'])) {
                    $content .= self::field($field_key, $field_data);
                }
            }

            $content .= '</div>';
        }

        return $content;
    }

    /**
     * 表单内容显示函数
     *
     * @since 1.0.0
     */
    private function _display_field($input_id, $input_css, $data)
    {
        $defaults = [
            'name'          => $input_id,
            'type'          => 'text',
            'rows'          => 5,
            'option'        => [],
            'value'         => '',
            'selected'      => 0,
            'show_option_value' => 0,
            'show_option_all' => ''
        ];
        $input_data = wp_parse_args($data['field'], $defaults);

        $this->attributes   = [];
        $this->input_field  = '';

        // 判断表单类型构建表单内容
        switch ($input_data['type']) {
            case 'textarea':
                $this->textarea($input_id, $input_css, $input_data);
                break;
            case 'select':
                $this->select($input_id, $input_css, $input_data);
                break;
            case 'datalist':
                $this->datalist($input_id, $input_css, $input_data);
                break;
            default:
                $this->input($input_id, $input_css, $input_data);
                break;
        }

        /** 判断显示表单前缀和后缀内容 */
        if (isset($data['before']) || isset($data['after'])) {

            // 添加前缀内容
            if (isset($data['before'])) {
                $this->input_field = $data['before'] . $this->input_field;
            }

            // 添加后缀内容
            if (isset($data['after'])) {
                $this->input_field .= $data['after'];
            }

            // 使用组包裹元素
            $this->input_field = sprintf('<div class="input-group">%1$s</div>', $this->input_field);
        }

        /** 判断显示表单标题 */
        if (isset($data['title'])) {
            $this->_label($input_id, $input_data['type'], $data['title']);
        }

        /** 判断显示表单简介 */
        if (isset($data['desc'])) {
            $this->input_field .= sprintf('<p class="description">%1$s</p>', $data['desc']);
        }

        // 返回表单内容
        return $this->input_field;
    }

    /**
     * 设定表单标题函数
     *
     * @since 1.0.0
     * @param string $input_id      表单 ID
     * @param string $input_type    表单类型，checkbox 和 radio 类型需要特殊标签
     * @param string $input_title   表单标题内容
     */
    private function _label($input_id, $input_type, $input_title)
    {
        if (empty($input_title)) {
            return;
        }

        /**
         * 判断 checkbox 和 radio 类型
         * 设定标题标签模版
         */
        if (in_array($input_type, ['checkbox', 'radio'])) {
            $input_label = '%3$s <label class="wwpo__checkbox-label" for="%1$s">%2$s</label>';
        } else {
            $input_label = '<label class="wwpo__input-label" for="%1$s">%2$s</label> %3$s';
        }

        $this->input_field = sprintf($input_label, esc_attr($input_id), $input_title, $this->input_field);
    }

    /**
     * 设定表单属性函数
     *
     * @since 1.0.0
     * @param string    $input_id       表单 ID
     * @param string    $input_css      表单样式
     * @param array     $input_data
     * {
     *  表单内容参数
     *  @var string name        表单名
     *  @var string readonly    只读属性
     *  @var string disabled    禁用属性
     *  @var string checked     checkbox/radio 类型的选中值
     *  @var string placeholder 表单占用符标题
     * }
     */
    protected function _attributes($input_id, $input_css, $input_data)
    {
        if (isset($input_data['name'])) {
            $this->attributes[] = sprintf('name="%s"', esc_attr($input_data['name']));
        }

        if (isset($input_css)) {
            $this->attributes[] = sprintf('class="%s"', esc_attr($input_css));
        }

        if (isset($input_id)) {
            $this->attributes[] = sprintf('id="%s"', esc_attr($input_id));
        }

        if (isset($input_data['readonly'])) {
            $this->attributes[] = wp_readonly($input_data['readonly'], true, false);
        }

        if (isset($input_data['disabled'])) {
            $this->attributes[] = disabled($input_data['disabled'], true, false);
        }

        if (isset($input_data['checked'])) {
            $this->attributes[] = checked($input_data['checked'], true, false);
        }

        if (isset($input_data['placeholder'])) {
            $this->attributes[] = sprintf('placeholder="%s"', $input_data['placeholder']);
        }

        if (isset($input_data['dataset'])) {
            foreach ($input_data['dataset'] as $dataset_key => $dataset_value) {
                $this->attributes[] = sprintf('data-%s="%s"', esc_attr($dataset_key), esc_attr($dataset_value));
            }
        }

        if (empty($this->attributes)) {
            return;
        }

        return implode(' ', $this->attributes);
    }
}

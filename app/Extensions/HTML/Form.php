<?php

namespace App\Extensions\HTML;

class Form extends \Illuminate\Html\FormBuilder
{
    public function checkboxInline($name, $value = 1, $checked = null, $options = [], $labelText = null, $labelOptions = [])
    {
        $this->labels[] = $name;

        $labelOptions = $this->html->attributes($labelOptions);

        $labelText = e($this->formatLabel($name, $labelText));

        return '<label ' . $labelOptions . '>' . $this->checkable('checkbox', $name, $value, $checked, $options) . $labelText . '</label>';
    }

    public function multiselect($name, $data = [], $options = [])
    {
        $options['id'] = $this->getIdAttribute($name, $options);

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $html = [];

        foreach ($data['options'] as $option) {
            if (isset($option['optgroup'])) {
                $html[] = $this->multiselectOptionGroup($data, $option['optgroup']);
            }

            $html[] = $this->multiselectOption($data, $option);
        }

        $options = $this->html->attributes($options);

        $list = implode('', $html);

        return "<select multiple {$options}>{$list}</select>";
    }

    protected function multiselectOptionGroup($data, $options)
    {
        $html = [];

        foreach ($options as $option) {
            $html[] = $this->multiselectOption($data, $option);
        }

        return '<optgroup label="' . e($option[$data['name']]) . '">' . implode('', $html) . '</optgroup>';
    }

    protected function multiselectOption($data, $option)
    {
        $selected = $this->getSelectedValue($option[$data['id']], $data['selected']);

        $options = array('value' => e($option[$data['id']]), 'selected' => $selected, 'data-sub-text' => ($data['subText'] ? e($option[$data['subText']]) : ''));

        return '<option' . $this->html->attributes($options) . '>' . e($option[$data['name']]) . '</option>';
    }
}

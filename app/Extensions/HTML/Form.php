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
}

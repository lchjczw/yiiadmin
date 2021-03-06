<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 2017/2/11
 * Time: 下午9:43
 */

namespace backend\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends \yii\widgets\ActiveField
{
    public function staticControl($options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeStaticControl($this->model, $this->attribute, $options);
        return $this;
    }

    public function suffix($suffix = '', $suffixType = 'addon', $size = 300)
    {
        $size = !empty($size) ? "input-group-{$size} " : '';
        $this->template = "{label}\n<div class=\"input-group $size\">{input}\n<div class=\"input-group-" . $suffixType . "\">" . $suffix . "</div></div>\n{hint}\n{error}";
        return $this;
    }

    public function prefix($prefix = '', $prefixType = 'addon', $size = 300)
    {
        $size = !empty($size) ? "input-group-{$size} " : '';
        $this->template = "{label}\n<div class=\"input-group $size\"><div class=\"input-group-" . $prefixType . "\">" . $prefix . "</div>\n{input}</div>\n{hint}\n{error}";
        return $this;
    }
}
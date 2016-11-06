<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 2016/11/6
 * Time: 下午3:55
 */

namespace common\behaviors;


Trait JsonTrait
{
    public function renderJson($status = 1, $message = '', $data = [])
    {
        \Yii::$app->response->format = 'json';
        return compact('status', 'message', 'data');
    }
}
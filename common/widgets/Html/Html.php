<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.12.2017
 * Time: 15:11
 */

namespace common\widgets\Html;

use Yii;

class Html extends \yii\bootstrap\Html
{
    public static function updateButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Update');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-primary'], $options);
        return parent::submitButton($title, $options);
    }

    public static function createButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Create');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-success'], $options);
        return parent::submitButton($title, $options);
    }

    public static function nextButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Next');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-primary'], $options);
        return parent::submitButton($title, $options);
    }
}
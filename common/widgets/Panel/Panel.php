<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 12.09.2017
 * Time: 9:44
 */

namespace common\widgets\Panel;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет `Bootstrap` панели.
 *
 * ```php
 *     <?= Panel::widget([
 *         'label' => 'Builds',
 *         'content' => $this->render('_form', ['modelForm' => $modelForm]),
 *     ]) ?>
 * ```
 */
class Panel extends Widget
{
    /**
     * @var string Заголовок панели, по умолчанию `Header`.
     */
    public $label = 'Header';
    /**
     * @var string Контент панели.
     */
    public $content = '';

    /**
     * Выполнение виджета
     */
    public function run()
    {
        return Html::tag('div', $this->label() . $this->content(), ['class' => 'panel panel-default']);
    }

    protected function label()
    {
        return Html::tag('div', Html::tag('h3', $this->label, ['class' => 'panel-title']), ['class' => 'panel-heading']);
    }

    protected function content()
    {
        return Html::tag('div', $this->content, ['class' => 'panel-body']);
    }
}
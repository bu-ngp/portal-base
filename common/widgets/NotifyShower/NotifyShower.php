<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 15:12
 */

namespace common\widgets\NotifyShower;


use common\widgets\CardList\assets\CardListAsset;
use common\widgets\NotifyShower\assets\NotifyShowerAsset;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class NotifyShower extends Widget
{
    const SUCCESS = 'success';
    const ERROR = 'danger';

    static public $messageContainer = [];

    static public function message($message, $type = self::ERROR)
    {
        if (is_string($message) && !empty($message)) {
            self::$messageContainer[] = [
                'type' => $type,
                'message' => $message,
            ];

            return $type !== self::ERROR;
        }

        return true;
    }

    static public function hasErrors()
    {
        return array_filter(self::$messageContainer, function ($value) {
            return $value['type'] === self::ERROR;
        });
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerAssets();
        echo $this->initLayout();
        $this->initJsNotify();
    }

    protected function registerAssets()
    {
        NotifyShowerAsset::register(self::getView());
    }

    protected function initLayout()
    {
        return Html::tag('div', $this->getMessagesHTML(), ['id' => $this->id, 'class' => 'wk-notify-shower']);
    }

    protected function getMessagesHTML()
    {
        $html = '';

        foreach (self::$messageContainer as $value) {
            $html .= "<li message-type=\"{$value['type']}\">{$value['message']}</li>";
        }

        return $html ? "<ul>$html</ul>" : '';
    }

    protected function initJsNotify()
    {
        $view = $this->getView();

        $settings = ['type' => 'danger'];
        $settings = json_encode(array_filter($settings), JSON_UNESCAPED_UNICODE);

        $view->registerJs("$('div.wk-notify-shower>ul li').each(function() { $.notify({message: $(this).text(), type: $(this).attr('message-type')}, $settings); });  ");
    }

}
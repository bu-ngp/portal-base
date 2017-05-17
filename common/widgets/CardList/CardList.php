<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 15:12
 */

namespace common\widgets\CardList;


use common\widgets\CardList\assets\CardListAsset;
use Exception;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\web\View;

class CardList extends Widget
{
    const RED_STYLE = 'wk-red-style';
    const BLUE_STYLE = 'wk-blue-style';
    const GREEN_STYLE = 'wk-green-style';
    const YELLOW_STYLE = 'wk-yellow-style';
    const GREY_STYLE = 'wk-grey-style';

    public $url;
    public $items;

    public function init()
    {
        parent::init();
        $this->registerTranslations();
        if (isset($this->items) && !is_array($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'items must be Array'));
        }

        if (empty($this->url) && empty($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'url or items must be passed'));
        }
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerAssets();
        echo $this->initLayout();
        $view = $this->getView();

        $options = (object)[
            'url' => $this->url,
            'items' => $this->items,
            'linkName' => Yii::t('wk-widget', 'Follow the link'),
        ];

        $optionsReplaced = str_replace('object', json_encode($options, JSON_UNESCAPED_UNICODE), file_get_contents(__DIR__ . '/assets/js/init.js'));

        $idReplaced = str_replace('id-widget', $this->id, $optionsReplaced);

        $view->registerJs(file_get_contents(__DIR__ . '/assets/js/wkcardlist.ru.js'), View::POS_END);
        $view->registerJs($idReplaced, View::POS_END);
    }

    protected function registerAssets()
    {
        CardListAsset::register(self::getView());
    }

    protected function initLayout()
    {
        return Html::tag('div', '', ['id' => $this->id]);
    }

    protected function renderCards()
    {
        $content = '';

        if (isset($this->items)) {
            /** @var array $card */
            foreach ($this->items as $index => $card) {
                if (!is_array($card)) {
                    throw new Exception(Yii::t('wk-widget', "Card by index {index} must be Array", ['index' => $index]));
                }

                $content .= $this->renderCard($card);
//
//            if (($index + 1) % 3 == 0) {
//                $content .= $this->renderLineBlock();
//            }
            }
        }

        return $content;
    }


}
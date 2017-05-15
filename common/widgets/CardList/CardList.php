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

    public $items;

    public function init()
    {
        parent::init();
        $this->registerTranslations();
        if (!is_array($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'items must be Array'));
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
        $view->registerJs(str_replace('id-widget', $this->id, file_get_contents(__DIR__ . '/assets/js/init.js')), View::POS_END);
    }

    protected function registerAssets()
    {
        CardListAsset::register(self::getView());
    }

    protected function initLayout()
    {
        return Html::tag('div', $this->renderCards(), ['class' => 'row wk-widget-container', 'id' => $this->id]) . $this->renderScrollPager();
    }

    protected function renderCards()
    {
        $content = '';
        /** @var array $card */
        foreach ($this->items as $index => $card) {
            if (!is_array($card)) {
                throw new Exception(Yii::t('wk-widget', "Card by index {index} must be Array", ['index' => $index]));
            }

            $content .= $this->renderCard($card);

            if (($index + 1) % 3 == 0) {
                $content .= $this->renderLineBlock();
            }
        }
        return $content;
    }

    protected function renderCard($card)
    {
        $mediaContent = $this->createMedia($card['preview']);

        $titleContent = Html::tag('h2', Html::encode($card['title']), ['class' => 'pmd-card-title-text'])
            . Html::tag('span', Html::encode($card['description']), ['class' => 'pmd-card-subtitle-text']);

        $actionsContent = Html::a(Yii::t('wk-widget', 'Follow the link'), $card['link'], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary']);

        $media = Html::tag('div', $mediaContent, ['class' => 'pmd-card-media ' . $card['styleClass']]);

        $title = Html::tag('div', $titleContent, ['class' => 'pmd-card-title']);
        $actions = Html::tag('div', $actionsContent, ['class' => 'pmd-card-actions']);

        $content = Html::tag('div', $media . $title . $actions, ['class' => 'pmd-card pmd-card-default pmd-z-depth']);

        return Html::tag('div', $content, ['class' => 'col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show']);
    }

    protected function createMedia($preview)
    {
        if (is_array($preview)) {
            return FA::icon($preview['FAIcon'], ['class' => 'wk-style']);
        }
        return Html::img($preview, ['class' => 'img-responsive']);
    }

    protected function renderScrollPager()
    {
        return Html::tag('div', FA::icon(FA::_COG)->size(FA::SIZE_4X)->spin(), ['id' => $this->id . '-scroll-pager', 'class' => 'wk-widget-pager', 'style' => 'display: none;']);
    }

    protected function renderLineBlock()
    {
        return Html::tag('div', '', ['class' => 'col-xs-12 wk-widget-card wk-widget-hide']);
    }
}
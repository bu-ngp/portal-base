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
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\db\ActiveRecord;
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
    public $cardsPerPage;
    public $language;
    /** @var  ActiveRecord */
    public $search;
    public $popularity;

    public function init()
    {
        parent::init();
        // $this->registerTranslations();
        if (isset($this->items) && !is_array($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'items must be Array'));
        }

        if (empty($this->url) && empty($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'url or items must be passed'));
        }

        if (empty($this->search)) {
            $this->search = false;
        }

        if (empty($this->popularity)) {
            $this->popularity = false;
        }
    }
//
//    public function registerTranslations()
//    {
//        $i18n = Yii::$app->i18n;
//        $i18n->translations['wk-widget'] = [
//            'class' => 'yii\i18n\PhpMessageSource',
//            'sourceLanguage' => 'en-US',
//            'basePath' => __DIR__ . '/messages',
//        ];
//    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerAssets();
        echo $this->initLayout();
        $view = $this->getView();

        $options = [
            'url' => $this->url,
            'items' => $this->items,
            'popularity' => $this->popularity,
            'cardsPerPage' => 6,
            'language' => 'ru',
        ];

        if ($this->search && !empty($this->search['modelSearch']) && $this->search['modelSearch'] instanceof ActiveRecord && !empty($this->search['searchAttributeName'])) {
            $options = array_replace_recursive($options, [
                'search' => true,
                'ajaxSearchName' => $this->search['modelSearch']->formName() . '[' . $this->search['searchAttributeName'] . ']',
            ]);
        } elseif ($this->search === true) {
            $options = array_replace_recursive($options, [
                'search' => true,
            ]);
        }

        $options = (object)array_filter($options);

        $optionsReplaced = str_replace('object', json_encode($options, JSON_UNESCAPED_UNICODE), file_get_contents(__DIR__ . '/assets/js/init.js'));

        $idReplaced = str_replace('id-widget', $this->id, $optionsReplaced);

        $language = substr($options->language, 0, 2);

        $view->registerJs(file_get_contents(__DIR__ . "/assets/js/wkcardlist.$language.js"), View::POS_END);
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

}
<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 15:12
 */

namespace common\widgets\CardList;


use common\widgets\CardList\assets\CardListAsset;
use common\widgets\PropellerAssets\PropellerAsset;
use Exception;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\db\ActiveRecord;

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
    /** @var  ActiveRecord */
    public $search = false;
    public $popularity = false;

    public function init()
    {
        $this->registerTranslations();
        if (isset($this->items) && !is_array($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'items must be Array'));
        }

        if (empty($this->url) && empty($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'url or items must be passed'));
        }

        parent::init();
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

        $options = [
            'url' => $this->url,
            'items' => $this->items,
            'popularity' => $this->popularity,
            'cardsPerPage' => 6,
            'messages' => [
                'followLinkMessage' => Yii::t('wk-widget', 'Follow the link'),
                'searchMessage' => Yii::t('wk-widget', 'Search'),
            ],
        ];

        $this->searchConfig($options);

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);
        $view->registerJs("$('#{$this->id}').wkcardlist($options);");
        PropellerAsset::setWidget(self::className());
    }

    protected function searchConfig(&$options)
    {
        if ($this->search === true) {
            $options = array_replace_recursive($options, [
                'search' => true,
            ]);
        } elseif (isset($this->search['modelSearch'])
            && $this->search['modelSearch'] instanceof ActiveRecord
            && isset($this->search['searchAttributeName'])
        ) {
            $options = array_replace_recursive($options, [
                'search' => true,
                'ajaxSearchName' => $this->search['modelSearch']->formName() . '[' . $this->search['searchAttributeName'] . ']',
            ]);
        }
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
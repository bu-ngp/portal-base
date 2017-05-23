<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 19.05.2017
 * Time: 16:19
 */

namespace common\widgets\CardList;


use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\Expression;

class CardListHelper
{
    /**
     * Метод создает карты на основе данных из модели ActiveRecord
     *
     * @param ActiveDataProvider $dataProvider Провайдер данных модели
     * @param string $titleAttribute Имя атрибута модели, из которого будет браться название карты
     * @param string $linkAttribute Имя атрибута модели, из которого будет браться ссылка, по умолчанию '#'
     * @param string $previewAttribute Имя атрибута модели, из которого будет браться класс иконки 'fa fa-cog' или url картинки, по умолчанию 'fa fa-cog'
     * @param string $descriptionAttribute Имя атрибута модели, из которого будет браться описание карты
     * @param string $styleClassAttribute Имя атрибута модели, из которого будет браться класс стиля карты
     * @param string $popularityID Имя атрибута модели, из которого будет браться ИД для сортировки по популярности, должно быть уникальным в рамках одного виджета
     * @return array массив карт
     */
    public static function createAjaxCards(ActiveDataProvider &$dataProvider, $titleAttribute, $linkAttribute = '', $previewAttribute = '', $descriptionAttribute = '', $styleClassAttribute = '', $popularityID = '')
    {
        $items = [];
        $dataProvider->getCount();

        if ($_GET['page'] <= $dataProvider->getPagination()->getPage() + 1) {
            foreach ($dataProvider->getModels() as $ar) {
                if (empty($previewAttribute)) {
                    $preview = [
                        'FAIcon' => 'cog',
                    ];
                } elseif (substr($previewAttribute, 0, 6) == 'fa fa-') {
                    $preview = [
                        'FAIcon' => substr($previewAttribute, 6),
                    ];
                } else {
                    $preview = $ar->$descriptionAttribute;
                }

                $items[] = [
                    'preview' => $preview,
                    'title' => Html::encode($ar->$titleAttribute),
                    'description' => empty($descriptionAttribute) ? '' : Html::encode($ar->$descriptionAttribute),
                    'styleClass' => empty($styleClassAttribute) ? CardList::GREY_STYLE : Html::encode($ar->$styleClassAttribute),
                    'link' => empty($linkAttribute) ? '#' : Html::encode($ar->$linkAttribute),
                    'popularityID' => empty($popularityID) ? '' : Html::encode($ar->$popularityID),
                ];
            }
        }

        return $items;
    }

    /**
     * Метод добавляет сортировку по популярности к модели поиска ActiveRecord
     *
     * @param ActiveQuery $activeQuery Запрос поиска
     * @param string $popularityIDFieldName Имя поля модели, которое содержит идентификаторы атрибута 'popularity-id' в HTML элементах карт
     */
    public static function applyPopularityOrder(ActiveQuery $activeQuery, $popularityIDFieldName)
    {
        $popularity = json_decode($_REQUEST['popularity']);
        if (!empty($popularity) && is_array($popularity)) {
            $activeQuery->addOrderBy(new Expression("FIELD(`$popularityIDFieldName`,'" . implode("','", $popularity) . "') DESC"));
        }

        $activeQuery->addOrderBy($popularityIDFieldName);
    }
}
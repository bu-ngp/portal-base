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
     * @return array массив карт
     */
    public static function createAjaxCards(ActiveDataProvider &$dataProvider, $titleAttribute, $linkAttribute = '', $previewAttribute = '', $descriptionAttribute = '', $styleClassAttribute = '')
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
                ];
            }
        }

        return $items;
    }
}
<?php

namespace ngp\services\models;

use common\widgets\CardList\CardList;
use ngp\services\rules\TilesRules;
use ngp\services\validators\ThumbnailFilter;
use ngp\services\validators\ThumbnailFilterValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use domain\behaviors\BlameableBehavior;
use ngp\services\forms\TilesForm;

/**
 * This is the model class for table "{{%tiles}}".
 *
 * @property integer $tiles_id
 * @property string $tiles_name
 * @property string $tiles_description
 * @property string $tiles_keywords
 * @property string $tiles_link
 * @property string $tiles_thumbnail
 * @property string $tiles_icon
 * @property string $tiles_icon_color
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 */
class Tiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tiles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(TilesRules::client(), [
            //    [['tiles_thumbnail'], ThumbnailFilterValidator::className()],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tiles_name' => Yii::t('ngp/tiles', 'Tiles Name'),
            'tiles_description' => Yii::t('ngp/tiles', 'Tiles Description'),
            'tiles_keywords' => Yii::t('ngp/tiles', 'Tiles Keywords'),
            'tiles_link' => Yii::t('ngp/tiles', 'Tiles Link'),
            'tiles_thumbnail' => Yii::t('ngp/tiles', 'Tiles Thumbnail'),
            'tiles_icon' => Yii::t('ngp/tiles', 'Tiles Icon'),
            'tiles_icon_color' => Yii::t('ngp/tiles', 'Tiles Icon Color'),
            'created_at' => Yii::t('domain/base', 'Created At'),
            'updated_at' => Yii::t('domain/base', 'Updated At'),
            'created_by' => Yii::t('domain/base', 'Created By'),
            'updated_by' => Yii::t('domain/base', 'Updated By'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    public static function create(TilesForm $form)
    {
        return new self([
            'tiles_name' => $form->tiles_name,
            'tiles_description' => $form->tiles_description,
            'tiles_link' => $form->tiles_link,
            'tiles_thumbnail' => $form->tiles_thumbnail,
            'tiles_icon' => $form->tiles_icon,
            'tiles_icon_color' => $form->tiles_icon_color,
            'tiles_keywords' => $form->tiles_keywords,
        ]);
    }

    public function edit(TilesForm $form)
    {
        $this->tiles_name = $form->tiles_name;
        $this->tiles_description = $form->tiles_description;
        $this->tiles_link = $form->tiles_link;
        $this->tiles_thumbnail = $form->tiles_thumbnail;
        $this->tiles_icon = $form->tiles_icon;
        $this->tiles_icon_color = $form->tiles_icon_color;
        $this->tiles_keywords = $form->tiles_keywords;
    }

    public static function items()
    {
        return [
            'tiles_icon_color' => [
                CardList::BLUE_STYLE => 'Синий',
                CardList::GREY_STYLE => 'Серый',
                CardList::RED_STYLE => 'Красный',
                CardList::GREEN_STYLE => 'Зеленый',
                CardList::YELLOW_STYLE => 'Желтый',
            ],
        ];
    }
}

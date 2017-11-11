<?php

namespace ngp\services\models;

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
        return [
            [['tiles_name', 'tiles_link', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['tiles_name', 'tiles_keywords', 'tiles_link', 'tiles_thumbnail', 'tiles_icon', 'tiles_icon_color', 'created_by', 'updated_by'], 'string', 'max' => 255],
            [['tiles_description'], 'string', 'max' => 400],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tiles_id' => Yii::t('ngp/tiles', 'Tiles ID'),
            'tiles_name' => Yii::t('ngp/tiles', 'Tiles Name'),
            'tiles_description' => Yii::t('ngp/tiles', 'Tiles Description'),
            'tiles_keywords' =>  Yii::t('ngp/tiles', 'Tiles Keywords'),
            'tiles_link' => Yii::t('ngp/tiles', 'Tiles Link'),
            'tiles_thumbnail' => Yii::t('ngp/tiles', 'Tiles Thumbnail'),
            'tiles_icon' => Yii::t('ngp/tiles', 'Tiles Icon'),
            'tiles_icon_color' => Yii::t('ngp/tiles', 'Tiles Icon Color'),
            'created_at' => Yii::t('ngp/tiles', 'Created At'),
            'updated_at' => Yii::t('ngp/tiles', 'Updated At'),
            'created_by' => Yii::t('ngp/tiles', 'Created By'),
            'updated_by' => Yii::t('ngp/tiles', 'Updated By'),
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
            'created_at' => $form->created_at,
            'updated_at' => $form->updated_at,
            'created_by' => $form->created_by,
            'updated_by' => $form->updated_by,
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
        $this->created_at = $form->created_at;
        $this->updated_at = $form->updated_at;
        $this->created_by = $form->created_by;
        $this->updated_by = $form->updated_by;
    }

}

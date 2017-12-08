<?php

namespace ngp\services\forms;

use common\widgets\CardList\CardList;
use ngp\services\models\Tiles;
use ngp\services\rules\TilesRules;
use ngp\services\validators\ThumbnailFilterValidator;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class TilesForm extends Model
{
    public $tiles_name;
    public $tiles_description;
    public $tiles_keywords;
    public $tiles_link;
    public $tiles_thumbnail;
    public $tiles_icon;
    public $tiles_icon_color;

    public $tiles_thumbnail_x;
    public $tiles_thumbnail_x2;
    public $tiles_thumbnail_y;
    public $tiles_thumbnail_y2;
    public $tiles_thumbnail_w;
    public $tiles_thumbnail_h;
    /** @var  UploadedFile */
    public $imageFile;

    public function __construct(Tiles $tiles = null, $config = [])
    {
        if ($icon = Yii::$app->request->get('icon')) {
            $this->tiles_icon = $icon;
        }

        if ($tiles) {
            $this->tiles_name = $tiles->tiles_name;
            $this->tiles_description = $tiles->tiles_description;
            $this->tiles_keywords = $tiles->tiles_keywords;
            $this->tiles_link = $tiles->tiles_link;
            $this->tiles_thumbnail = $tiles->tiles_thumbnail;
            $this->tiles_icon = $tiles->tiles_icon;
            $this->tiles_icon_color = $tiles->tiles_icon_color;
        } else {
            $this->tiles_icon_color = CardList::BLUE_STYLE;
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return array_merge(TilesRules::client(), [
            [['tiles_thumbnail_x', 'tiles_thumbnail_x2', 'tiles_thumbnail_y', 'tiles_thumbnail_y2', 'tiles_thumbnail_w', 'tiles_thumbnail_h'], 'safe'],
            [['tiles_thumbnail_x', 'tiles_thumbnail_y'], 'default', 'value' => 0],
            [['tiles_thumbnail_w'], 'default', 'value' => 363],
            [['tiles_thumbnail_h'], 'default', 'value' => 209],
            [['imageFile'], 'file', 'extensions' => ['jpg', 'png']],
            [['imageFile'], ThumbnailFilterValidator::className(),
                'widthAttribute' => 'tiles_thumbnail_w',
                'heightAttribute' => 'tiles_thumbnail_h',
                'xAttribute' => 'tiles_thumbnail_x',
                'yAttribute' => 'tiles_thumbnail_y',
                'path' => Yii::getAlias('@thumbsPath'),
                'web' => Yii::getAlias('@web/thumbs'),
                'thumbnailAttribute' => 'tiles_thumbnail',
                'to' => [
                    [363, 209],
                    [165, 95],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return (new Tiles())->attributeLabels();
    }

    public function beforeValidate()
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
        return parent::beforeValidate();
    }
}
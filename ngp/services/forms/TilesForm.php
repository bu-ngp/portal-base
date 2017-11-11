<?php

namespace ngp\services\forms;

use ngp\services\models\Tiles;
use yii\base\Model;

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

    public function __construct(Tiles $tiles = null, $config = [])
    {
        if ($tiles) {
            $this->tiles_name = $tiles->tiles_name;
            $this->tiles_description = $tiles->tiles_description;
            $this->tiles_keywords = $tiles->tiles_keywords;
            $this->tiles_link = $tiles->tiles_link;
            $this->tiles_thumbnail = $tiles->tiles_thumbnail;
            $this->tiles_icon = $tiles->tiles_icon;
            $this->tiles_icon_color = $tiles->tiles_icon_color;
        }

        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return (new Tiles())->attributeLabels();
    }
}
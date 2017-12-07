<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.11.2017
 * Time: 15:12
 */


use ngp\assets\TilesAsset;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $icons array */
/* @var $redirectTo string */

$this->title = Yii::t('ngp/tiles', 'Choose Icon');
?>
    <div class="tiles-icons content-container">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-12 wk-tiles-icons">
                <?php
                foreach ($icons as $icon) {
                    echo '<div class="col-md-1"><a href="' . Url::to(['tiles/' . $redirectTo, 'icon' => $icon]) . '">' . FA::icon($icon) . '</a></div>';
                }
                ?>
            </div>
        </div>
    </div>

<?php TilesAsset::register($this) ?>
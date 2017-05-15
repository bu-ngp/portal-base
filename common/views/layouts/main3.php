<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\assets\AppCommonAsset;
use common\widgets\Alert;

if (file_exists(Yii::getAlias('@app') . '/views/layouts/assets.php')) {
    $this->beginContent('@app/views/layouts/assets.php');
    $this->endContent();
} else {
    AppCommonAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl ?>/favicon.ico" type="image/x-icon"/>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper" style="opacity: 0;">
    <div class="container">
        <?php
        NavBar::begin([
            'brandLabel' => 'My Company',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-inverse navbar-fixed-top pmd-navbar pmd-z-depth',
            ],
        ]);
        $menuItems = [
            ['label' => 'Главная', 'url' => Yii::$app->urlManagerFrontend->createUrl(['/']), 'linkOptions' => ['class' => 'pmd-ripple-effect']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Войти', 'url' => ['/login'], 'linkOptions' => ['class' => 'pmd-ripple-effect']];
        } else {
            $menuItems[] = '<li>'
                . Html::beginForm(Yii::$app->urlManagerFrontend->createUrl(['/site/logout']), 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->person_username . ')',
                    ['class' => 'btn btn-link logout', 'style' => 'padding-top: 20px; padding-bottom: 20px; line-height: 24px;']
                )
                . Html::endForm()
                . '</li>';
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>

        <div class="wrap">
            <style>
                .grid-item.g1 {
                    /*float: left;*/
                    height: 400px;
                    border: 2px solid hsla(0, 0%, 0%, 0.5);
                    background-color: #7a7a7a;
                    opacity: 0;
                }

                .grid-item.g1.new1 {
                    opacity: 1;
                }

                .stamp1 {
                    height: 5px;
                    opacity: 0;
                }
            </style>
            <div id="c1" class="row grid">
                <div class="grid-item g1 grid-sizer col-xs-12 col-sm-6 col-md-4"></div>
                <div style="height: 350px;" class="grid-item g1 col-xs-12 col-sm-6 col-md-4 "></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item stamp1 col-xs-12"></div>
                <div style="height: 300px;" class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item stamp1 col-xs-12"></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item g1 col-xs-12 col-sm-6 col-md-4"></div>
                <div class="grid-item stamp1 col-xs-12"></div>
            </div>
            <div id="loadmoreajaxloader" style="display: none;"></div>
        </div>

    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

</div>
<?php $this->endBody() ?>
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
<script>
    $(document).ready(function () {
        $('#c1').imagesLoaded(function () {
            $('#c1').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                isAnimated: true,
                horizontalOrder: true,
                percentPosition: true
            });
        });
        $(window).scroll(function () {
            if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                $('div#loadmoreajaxloader').show();
                $.ajax({
                    url: "/wk-portal_dev/site/test",
                    success: function (html) {
                        if (html) {
                            var $items = $(html);
                            $('#c1').append($items);
                            $('#c1').masonry('appended', $items);
//                            $('#c1').masonry('addItems', $items);
//                            $('#c1').masonry('layout');
                            $($items).animate({opacity: 1}, 500);
                            $('div#loadmoreajaxloader').hide();
                        } else {
                            $('div#loadmoreajaxloader').html('<center>No more posts to show.</center>');
                        }
                    }
                });
            }
        });

        $(".grid-item.g1").each(function (i, elem) {
            var stallFor = 100 * parseInt(i);
            $(this).delay(stallFor).animate({opacity: 1}, 500);
        });

    });
</script>
</body>
</html>
<?php $this->endPage() ?>

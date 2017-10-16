<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\PropellerAssets\PropellerAsset;
use common\widgets\ReportLoader\assets\ReportLoaderAsset;
use common\widgets\ReportLoader\ReportLoader;
use lo\modules\noty\Wrapper;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\assets\AppCommonAsset;

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
    <base href="<?= Yii::$app->request->getBaseUrl() . '/' ?>">
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
            [
                'label' => 'Система',
                'url' => '#',
                'linkOptions' => [
                    'class' => 'pmd-ripple-effect dropdown-toggle',
                    'data-sidebar' => 'true',
                ],
                'options' => ['class' => 'dropdown pmd-dropdown'],
                'items' => [
                    [
                        'label' => 'Затребованные отчеты',
                        'url' => '#',
                        'linkOptions' => [
                            'class' => 'pmd-ripple-effect wk-widget-reports-loader',
                            'data-target' => '#wk-Report-Loader',
                            'data-toggle' => 'modal',
                        ],
                    ],
                    [
                        'label' => 'Обновления',
                        'url' => '#',
                        'linkOptions' => ['class' => 'pmd-ripple-effect'],
                    ],
                ],
            ],

        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Войти', 'url' => Yii::$app->urlManagerAdmin->createUrl('login'), 'linkOptions' => ['class' => 'pmd-ripple-effect']];
        } else {
            $menuItems[] = '<li>'
                . Html::beginForm(Yii::$app->urlManagerFrontend->createUrl(['/site/logout']), 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->person_fullname . ')',
                    ['class' => 'btn btn-link logout', 'style' => 'line-height: 20px;']
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
            <?= Breadcrumbs::widget(/*['defaultShow' => false]*/)  /*Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])*/ ?>
            <?= $content ?>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container-footer">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?= ReportLoader::widget(['id' => 'wk-Report-Loader']); ?>
<?= Wrapper::widget([
    'layerClass' => 'lo\modules\noty\layers\Toastr',
]) ?>

<?php
    PropellerAsset::setWidget('yii\bootstrap\NavBar');
    PropellerAsset::register($this);
    $this->endBody()
?>
</body>
</html>
<?php $this->endPage() ?>
